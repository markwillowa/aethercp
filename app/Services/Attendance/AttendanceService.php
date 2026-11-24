<?php

namespace App\Services\Attendance;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Models\AttendanceCampaign;
use App\Models\AttendanceCampaignDay;
use App\Models\AttendanceCampaignItem;
use App\Models\AttendanceClaim;
use App\Models\AttendanceDelivery;
use App\Models\AttendanceParticipation;
use App\Models\PlayerInboxItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttendanceService
{
    public function __construct(
        protected AttendanceDeliveryService $deliveryService,
        protected CharacterRepositoryInterface $characterRepo,
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Get all active campaigns for the player, including participation state.
     */
    public function getActiveCampaignsForUser(User $user, ?int $charId = null): array
    {
        $campaigns = AttendanceCampaign::where('status', 'active')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $result = [];
        foreach ($campaigns as $camp) {
            if ($camp->isActive()) {
                $participation = $this->getOrCreateParticipation($camp, $user, $charId);
                $eligibility = $this->checkEligibility($camp, $user, $charId);
                $days = $this->getCampaignDaysWithStatus($camp, $participation);

                $result[] = [
                    'campaign' => $camp,
                    'participation' => $participation,
                    'eligibility' => $eligibility,
                    'days' => $days,
                    'next_claimable_day' => $this->getNextClaimableDay($camp, $participation, $days),
                    'can_claim_today' => $this->canClaimToday($camp, $participation, $eligibility),
                ];
            }
        }

        return $result;
    }

    /**
     * Resolve or initialize participation for user/character.
     */
    public function getOrCreateParticipation(AttendanceCampaign $campaign, User $user, ?int $charId = null): AttendanceParticipation
    {
        $resolvedCharId = $campaign->isAccountScoped() ? 0 : ($charId ?: 0);

        return AttendanceParticipation::firstOrCreate(
            [
                'campaign_id' => $campaign->id,
                'account_id' => (int) $user->account_id,
                'character_id' => (int) $resolvedCharId,
            ],
            [
                'user_id' => $user->id,
                'current_progress' => 0,
                'current_streak' => 0,
                'highest_streak' => 0,
                'total_claimed_days' => 0,
                'started_at' => now(),
                'status' => 'active',
            ]
        );
    }

    /**
     * Validate player eligibility against campaign requirements.
     */
    public function checkEligibility(AttendanceCampaign $campaign, User $user, ?int $charId = null): array
    {
        // 1. Account age requirement
        if ($campaign->min_account_age_days > 0) {
            $ageDays = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
            if ($ageDays < $campaign->min_account_age_days) {
                return [
                    'eligible' => false,
                    'reason' => "Requires account age of at least {$campaign->min_account_age_days} days (Current: {$ageDays} days).",
                ];
            }
        }

        // 2. Base level requirement
        if ($campaign->min_base_level > 1) {
            $char = $charId ? $this->characterRepo->findById($charId) : null;
            if (!$char) {
                $characters = $this->characterRepo->getByAccountId((int) $user->account_id);
                $highestLevel = collect($characters)->max('base_level') ?? 1;
            } else {
                $highestLevel = (int) $char->base_level;
            }

            if ($highestLevel < $campaign->min_base_level) {
                return [
                    'eligible' => false,
                    'reason' => "Requires at least Base Level {$campaign->min_base_level} (Highest: {$highestLevel}).",
                ];
            }
        }

        return ['eligible' => true, 'reason' => null];
    }

    /**
     * Compute the current date string in the campaign's configured timezone.
     */
    public function getCampaignTodayDate(AttendanceCampaign $campaign): string
    {
        $tz = $campaign->timezone ?: 'UTC';
        $now = Carbon::now($tz);

        // If daily reset hour is configured (e.g. 4 AM), shift accordingly
        if ($campaign->daily_reset_hour > 0) {
            if ($now->hour < $campaign->daily_reset_hour) {
                $now->subDay();
            }
        }

        return $now->toDateString();
    }

    /**
     * Check if the user is eligible to claim today's reward.
     */
    public function canClaimToday(AttendanceCampaign $campaign, AttendanceParticipation $participation, array $eligibility): bool
    {
        if (!$eligibility['eligible'] || !$campaign->isActive() || $participation->status === 'completed') {
            return false;
        }

        $todayDate = $this->getCampaignTodayDate($campaign);

        // Already claimed on this campaign day
        if ($participation->last_attendance_date && $participation->last_attendance_date->toDateString() === $todayDate) {
            return false;
        }

        // Completed all days and non-repeatable
        if ($participation->total_claimed_days >= $campaign->duration_days && !$campaign->repeatable) {
            return false;
        }

        return true;
    }

    /**
     * Build calendar days with status for player UI.
     */
    public function getCampaignDaysWithStatus(AttendanceCampaign $campaign, AttendanceParticipation $participation): array
    {
        $campaignDays = $campaign->days()->with('items')->get()->keyBy('day_number');
        $claimedDays = $participation->claims()->pluck('day_number')->flip()->all();
        $totalDays = $campaign->duration_days;
        $progressionMode = $campaign->progression_mode; // consecutive, cumulative, scheduled
        $todayDate = $this->getCampaignTodayDate($campaign);
        $alreadyClaimedToday = $participation->last_attendance_date && $participation->last_attendance_date->toDateString() === $todayDate;

        $nextDayNumber = $participation->total_claimed_days + 1;
        $days = [];

        for ($d = 1; $d <= $totalDays; $d++) {
            $dayModel = $campaignDays->get($d);
            $items = $dayModel ? $dayModel->items : collect();
            $isClaimed = isset($claimedDays[$d]);

            $status = 'locked';
            if ($isClaimed) {
                $status = 'claimed';
            } elseif (!$alreadyClaimedToday && $d === $nextDayNumber) {
                $status = 'available';
            } elseif ($d < $nextDayNumber) {
                // If consecutive mode and missed
                $status = ($progressionMode === 'consecutive' && $campaign->reset_rule === 'reset_to_start') ? 'missed' : 'available';
            }

            $days[] = [
                'day_number' => $d,
                'title' => $dayModel?->title ?: "Day {$d}",
                'description' => $dayModel?->description,
                'is_milestone' => (bool) ($dayModel?->is_milestone || ($d % 7 === 0) || $d === $totalDays),
                'status' => $status,
                'items' => $items,
            ];
        }

        return $days;
    }

    public function getNextClaimableDay(AttendanceCampaign $campaign, AttendanceParticipation $participation, array $days): ?array
    {
        foreach ($days as $d) {
            if ($d['status'] === 'available') {
                return $d;
            }
        }
        return null;
    }

    /**
     * Perform an idempotent claim of a daily attendance reward.
     */
    public function claimReward(AttendanceCampaign $campaign, User $user, int $dayNumber, ?int $receivingCharId = null): array
    {
        $participation = $this->getOrCreateParticipation($campaign, $user, $receivingCharId);
        $eligibility = $this->checkEligibility($campaign, $user, $receivingCharId);

        if (!$eligibility['eligible']) {
            return ['success' => false, 'message' => $eligibility['reason'] ?: 'Eligibility check failed.'];
        }

        if (!$campaign->isActive()) {
            return ['success' => false, 'message' => 'This reward campaign is not currently active.'];
        }

        $todayDate = $this->getCampaignTodayDate($campaign);

        // Check if already claimed today
        if ($participation->last_attendance_date && $participation->last_attendance_date->toDateString() === $todayDate) {
            return ['success' => false, 'message' => 'You have already claimed your daily reward today. Please come back tomorrow!'];
        }

        // Validate day number matches progression
        $expectedDay = $participation->total_claimed_days + 1;
        if ($dayNumber !== $expectedDay && $campaign->progression_mode !== 'scheduled') {
            return ['success' => false, 'message' => "Invalid reward day requested (Expected Day {$expectedDay})."];
        }

        // Validate receiving character belongs to this account
        $targetChar = null;
        if ($receivingCharId) {
            $char = $this->characterRepo->findById($receivingCharId);
            if (!$char || (int) $char->account_id !== (int) $user->account_id) {
                return ['success' => false, 'message' => 'Selected receiving character does not belong to your account.'];
            }
            $targetChar = $char;
        } else {
            // Auto-select first character belonging to account if available
            $characters = $this->characterRepo->getByAccountId((int) $user->account_id);
            $targetChar = $characters[0] ?? null;
        }

        $campaignDay = $campaign->days()->where('day_number', $dayNumber)->with('items')->first();
        if (!$campaignDay) {
            return ['success' => false, 'message' => "Reward schedule for Day {$dayNumber} has not been configured."];
        }

        // Unique idempotency key
        $idempotencyKey = "att_claim_{$participation->id}_{$dayNumber}_{$todayDate}";

        return DB::transaction(function () use ($campaign, $participation, $campaignDay, $dayNumber, $user, $targetChar, $todayDate, $idempotencyKey) {
            // Prevent duplicate claim race condition
            $existingClaim = AttendanceClaim::where('idempotency_key', $idempotencyKey)->first();
            if ($existingClaim) {
                return ['success' => false, 'message' => 'This daily reward has already been claimed.'];
            }

            // Streak calculations
            $streak = 1;
            if ($participation->last_attendance_date) {
                $lastDate = Carbon::parse($participation->last_attendance_date);
                $diffDays = $lastDate->diffInDays(Carbon::parse($todayDate));
                if ($diffDays === 1) {
                    $streak = $participation->current_streak + 1;
                } elseif ($diffDays > 1 && $campaign->progression_mode === 'consecutive' && $campaign->reset_rule === 'reset_to_start') {
                    $streak = 1;
                } else {
                    $streak = $participation->current_streak + 1;
                }
            }

            $highestStreak = max($streak, $participation->highest_streak);
            $totalClaimed = $participation->total_claimed_days + 1;

            $claim = AttendanceClaim::create([
                'participation_id' => $participation->id,
                'campaign_id' => $campaign->id,
                'campaign_day_id' => $campaignDay->id,
                'day_number' => $dayNumber,
                'account_id' => (int) $user->account_id,
                'character_id' => $targetChar ? (int) $targetChar->char_id : null,
                'claimed_at' => now(),
                'status' => 'delivered',
                'idempotency_key' => $idempotencyKey,
            ]);

            // Deliver all reward items for this day
            $deliveryResults = $this->deliveryService->deliverClaimItems($claim, $campaignDay->items, $user, $targetChar, $campaign);

            // Update participation record
            $isComplete = $totalClaimed >= $campaign->duration_days;
            $participation->update([
                'current_progress' => $totalClaimed,
                'current_streak' => $streak,
                'highest_streak' => $highestStreak,
                'total_claimed_days' => $totalClaimed,
                'last_attendance_date' => $todayDate,
                'last_claimed_at' => now(),
                'completed_at' => $isComplete ? now() : null,
                'status' => ($isComplete && !$campaign->repeatable) ? 'completed' : 'active',
            ]);

            return [
                'success' => true,
                'message' => "Successfully claimed Day {$dayNumber} rewards!",
                'claim' => $claim,
                'deliveries' => $deliveryResults,
                'streak' => $streak,
                'total_claimed' => $totalClaimed,
            ];
        });
    }
}
