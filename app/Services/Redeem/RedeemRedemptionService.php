<?php

namespace App\Services\Redeem;

use App\Contracts\EmulatorInterface;
use App\Models\ActivityLog;
use App\Models\RedeemCampaign;
use App\Models\RedeemCode;
use App\Models\RedeemCodeReward;
use App\Models\RedeemRedemption;
use App\Models\RedeemRewardDelivery;
use App\Models\User;
use App\Services\CashPoints\CashPointsService;
use App\Services\Escrow\EscrowInboxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RedeemRedemptionService
{
    public const MAX_ZENY = 2147483647;

    public function __construct(
        protected RedeemCodeService $codeService,
        protected CashPointsService $cashPointsService,
        protected EscrowInboxService $escrowService,
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Validate code, campaign, and account/character eligibility without mutating state.
     * Uses generic messages for unknown/inactive/expired codes to prevent enumeration.
     */
    public function previewCode(User $user, string $rawCode, ?int $characterId = null): array
    {
        $normalized = $this->codeService->normalizeCode($rawCode);
        if ($normalized === '') {
            return ['ok' => false, 'message' => 'Please enter a valid redeem code.'];
        }

        $hash = $this->codeService->hashCode($normalized);
        $code = RedeemCode::with(['campaign', 'rewards'])->where('code_hash', $hash)->first();

        if (!$code || !$code->campaign) {
            return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
        }

        $campaign = $code->campaign;
        if (!$campaign->isCurrentlyRedeemable()) {
            return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
        }

        if ($code->effectiveStatus() !== 'active') {
            return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
        }

        $eligibility = $this->checkEligibility($user, $campaign, $code, $characterId);
        if (!$eligibility['ok']) {
            return $eligibility;
        }

        $rewards = $this->resolveRewards($campaign, $code);
        if ($rewards->isEmpty()) {
            return ['ok' => false, 'message' => 'This code has no configured rewards.'];
        }

        $requiresCharacter = $rewards->contains(fn ($r) => $r->reward_type === 'zeny' && $r->delivery_scope === 'character')
            || $campaign->one_per_character
            || $campaign->min_base_level !== null
            || $campaign->max_base_level !== null
            || !empty($campaign->allowed_classes)
            || !empty($campaign->excluded_classes)
            || !empty($campaign->allowed_character_ids);

        return [
            'ok' => true,
            'code' => $code,
            'campaign' => $campaign,
            'rewards' => $rewards,
            'requires_character' => $requiresCharacter,
            'character' => $eligibility['character'] ?? null,
        ];
    }

    /**
     * Process a confirmed redemption atomically with transaction locking and idempotent reward deliveries.
     */
    public function redeem(User $user, string $rawCode, ?int $characterId = null, ?string $ipAddress = null): array
    {
        $normalized = $this->codeService->normalizeCode($rawCode);
        if ($normalized === '') {
            return ['ok' => false, 'message' => 'Please enter a valid redeem code.'];
        }

        if (empty($user->account_id)) {
            return ['ok' => false, 'message' => 'Your account is not linked to an in-game rAthena account.'];
        }

        $hash = $this->codeService->hashCode($normalized);

        $txResult = DB::transaction(function () use ($user, $hash, $characterId, $ipAddress) {
            $code = RedeemCode::where('code_hash', $hash)->lockForUpdate()->first();
            if (!$code) {
                return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
            }

            $campaign = RedeemCampaign::where('id', $code->campaign_id)->lockForUpdate()->first();
            if (!$campaign || !$campaign->isCurrentlyRedeemable() || $code->effectiveStatus() !== 'active') {
                return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
            }

            $eligibility = $this->checkEligibility($user, $campaign, $code, $characterId);
            if (!$eligibility['ok']) {
                return $eligibility;
            }

            $rewards = $this->resolveRewards($campaign, $code);
            if ($rewards->isEmpty()) {
                return ['ok' => false, 'message' => 'This code has no configured rewards.'];
            }

            $char = $eligibility['character'] ?? null;

            // Update code usage count atomically
            $code->redemptions_count = $code->redemptions_count + 1;
            if ($code->max_redemptions > 0 && $code->redemptions_count >= $code->max_redemptions) {
                $code->status = 'exhausted';
            }
            $code->save();

            $redemption = RedeemRedemption::create([
                'campaign_id' => $campaign->id,
                'redeem_code_id' => $code->id,
                'user_id' => $user->id,
                'account_id' => (int) $user->account_id,
                'username_snapshot' => $user->username ?? $user->name,
                'character_id' => $char ? (int) $char->char_id : null,
                'character_name_snapshot' => $char ? (string) $char->name : null,
                'code_hint' => $code->code_hint,
                'status' => 'processing',
                'delivery_status' => 'processing',
                'idempotency_key' => 'redemp_' . Str::uuid()->toString(),
                'ip_address' => $ipAddress ?: request()?->ip(),
                'reward_summary' => $rewards->map(fn ($r) => $r->formattedLabel())->values()->all(),
                'redeemed_at' => now(),
            ]);

            $deliveries = [];
            foreach ($rewards as $reward) {
                $deliveries[] = RedeemRewardDelivery::create([
                    'redemption_id' => $redemption->id,
                    'reward_id' => $reward->id,
                    'reward_type' => $reward->reward_type,
                    'idempotency_key' => "deliv_{$redemption->id}_{$reward->id}",
                    'status' => 'pending',
                    'attempts' => 0,
                    'account_id' => (int) $user->account_id,
                    'character_id' => $char ? (int) $char->char_id : null,
                    'amount' => (int) $reward->amount,
                    'item_id' => $reward->item_id,
                    'item_name' => $reward->item_name,
                    'payload' => [
                        'delivery_scope' => $reward->delivery_scope,
                        'refine' => $reward->refine,
                        'card0' => $reward->card0,
                        'card1' => $reward->card1,
                        'card2' => $reward->card2,
                        'card3' => $reward->card3,
                        'is_bound' => $reward->is_bound,
                        'custom_payload' => $reward->custom_payload,
                        'campaign_name' => $campaign->name,
                    ],
                ]);
            }

            return [
                'ok' => true,
                'redemption' => $redemption,
                'deliveries' => $deliveries,
            ];
        });

        if (!$txResult['ok']) {
            return $txResult;
        }

        /** @var RedeemRedemption $redemption */
        $redemption = $txResult['redemption'];
        foreach ($txResult['deliveries'] as $delivery) {
            $this->deliverSingleReward($delivery);
        }

        $this->refreshRedemptionStatuses($redemption);
        $redemption->refresh();

        $this->codeService->log(
            $user,
            'code.redeemed',
            "Redeemed {$redemption->code_hint} (status: {$redemption->status})",
            $redemption->campaign_id,
            $redemption->redeem_code_id,
            $redemption->id
        );
        ActivityLog::log('redeem.code_redeemed', "Redeemed code {$redemption->code_hint} ({$redemption->status}).", (int) $user->account_id);

        return [
            'ok' => $redemption->status === 'completed',
            'partial' => $redemption->status === 'partially_failed',
            'redemption' => $redemption->load('deliveries'),
            'message' => match ($redemption->status) {
                'completed' => 'Code redeemed successfully! All rewards have been delivered.',
                'partially_failed' => 'Code redeemed, but some rewards could not be delivered yet. An administrator can retry failed items.',
                default => 'Code redeemed, but reward delivery failed. Please contact support or wait for retry.',
            },
        ];
    }

    /**
     * Deliver a single reward record atomically and idempotently.
     */
    public function deliverSingleReward(RedeemRewardDelivery $delivery): bool
    {
        $locked = RedeemRewardDelivery::where('id', $delivery->id)
            ->whereIn('status', ['pending', 'failed', 'retrying'])
            ->update(['status' => 'processing']);

        if ($locked === 0) {
            return false;
        }

        $delivery->refresh();
        $delivery->attempts = $delivery->attempts + 1;

        try {
            $payload = $delivery->payload ?? [];
            $campaignName = (string) ($payload['campaign_name'] ?? 'Redeem Campaign');

            if ($delivery->reward_type === 'cash_points') {
                $newBalance = $this->cashPointsService->credit(
                    (int) $delivery->account_id,
                    (int) $delivery->amount,
                    "Redeem Code ({$campaignName})",
                    $delivery->idempotency_key
                );
                $delivery->destination_reference = "Account #{$delivery->account_id} #CASHPOINTS (New: " . number_format($newBalance) . " CP)";
            } elseif ($delivery->reward_type === 'item') {
                $inboxItem = $this->escrowService->depositItem((int) $delivery->account_id, [
                    'character_id' => $delivery->character_id,
                    'source_type' => 'redeem_code',
                    'source_id' => $delivery->redemption_id,
                    'reward_type' => 'item',
                    'item_id' => (int) $delivery->item_id,
                    'item_name' => $delivery->item_name,
                    'quantity' => max(1, (int) $delivery->amount),
                    'refine' => (int) ($payload['refine'] ?? 0),
                    'card0' => (int) ($payload['card0'] ?? 0),
                    'card1' => (int) ($payload['card1'] ?? 0),
                    'card2' => (int) ($payload['card2'] ?? 0),
                    'card3' => (int) ($payload['card3'] ?? 0),
                    'bound' => !empty($payload['is_bound']) ? 1 : 0,
                    'title' => "Redeem Reward: {$campaignName}",
                    'message' => "Reward from redeem code. Transfer safely to Storage or offline Character.",
                ]);
                $delivery->destination_reference = "Escrow Inbox #{$inboxItem->id}";
            } elseif ($delivery->reward_type === 'zeny') {
                $scope = (string) ($payload['delivery_scope'] ?? 'character');
                if ($scope === 'escrow') {
                    $inboxItem = $this->escrowService->depositItem((int) $delivery->account_id, [
                        'character_id' => $delivery->character_id,
                        'source_type' => 'redeem_code',
                        'source_id' => $delivery->redemption_id,
                        'reward_type' => 'zeny',
                        'zeny_amount' => (int) $delivery->amount,
                        'title' => "Redeem Reward: {$campaignName}",
                        'message' => 'Zeny reward deposited safely to Escrow Inbox.',
                    ]);
                    $delivery->destination_reference = "Escrow Inbox #{$inboxItem->id} (" . number_format((int) $delivery->amount) . ' Zeny)';
                } else {
                    $this->deliverZenySafely($delivery);
                }
            } elseif ($delivery->reward_type === 'custom') {
                if (!empty($payload['custom_payload']['simulate_failure'])) {
                    throw new \RuntimeException('Simulated custom reward delivery failure.');
                }
                $delivery->destination_reference = 'Custom reward processed: ' . ($payload['custom_payload']['label'] ?? 'Custom');
            }

            $delivery->status = 'delivered';
            $delivery->error_message = null;
            $delivery->delivered_at = now();
            $delivery->save();

            return true;
        } catch (\Throwable $e) {
            Log::error("Redeem delivery #{$delivery->id} failed: " . $e->getMessage());
            $delivery->status = 'failed';
            $delivery->error_message = $e->getMessage();
            $delivery->save();

            return false;
        }
    }

    public function retryDelivery(RedeemRewardDelivery $delivery, User $actor): bool
    {
        if (!in_array($delivery->status, ['failed', 'pending'], true)) {
            return false;
        }

        $delivery->update(['status' => 'retrying']);
        $ok = $this->deliverSingleReward($delivery);
        $this->refreshRedemptionStatuses($delivery->redemption);

        $this->codeService->log(
            $actor,
            'delivery.retried',
            "Retried delivery #{$delivery->id} (" . ($ok ? 'delivered' : 'failed') . ")",
            $delivery->redemption?->campaign_id,
            $delivery->redemption?->redeem_code_id,
            $delivery->redemption_id
        );

        return $ok;
    }

    protected function deliverZenySafely(RedeemRewardDelivery $delivery): void
    {
        $charId = (int) ($delivery->character_id ?? 0);
        if ($charId <= 0) {
            throw new \RuntimeException('A target character is required for Zeny delivery.');
        }

        $roConn = DB::connection('ragnarok');
        $charTable = $this->emulator->getTableName('char');

        $roConn->transaction(function () use ($roConn, $charTable, $charId, $delivery) {
            $char = $roConn->table($charTable)
                ->where('char_id', $charId)
                ->where('account_id', $delivery->account_id)
                ->lockForUpdate()
                ->first();

            if (!$char) {
                throw new \RuntimeException("Character #{$charId} not found for account #{$delivery->account_id}.");
            }
            if ((int) ($char->online ?? 0) > 0) {
                throw new \RuntimeException("Character \"{$char->name}\" is currently online. Log out to safely receive Zeny.");
            }

            $currentZeny = (int) $char->zeny;
            $addAmount = (int) $delivery->amount;
            if ($currentZeny + $addAmount > self::MAX_ZENY) {
                throw new \RuntimeException("Zeny balance limit exceeded for character \"{$char->name}\".");
            }

            $newZeny = $currentZeny + $addAmount;
            $roConn->table($charTable)->where('char_id', $charId)->update(['zeny' => $newZeny]);

            $delivery->destination_reference = "Character {$char->name} (#{$charId}) | +" . number_format($addAmount) . " Zeny";
        });
    }

    protected function refreshRedemptionStatuses(RedeemRedemption $redemption): void
    {
        $deliveries = $redemption->deliveries()->get();
        $total = $deliveries->count();
        $delivered = $deliveries->where('status', 'delivered')->count();
        $failed = $deliveries->where('status', 'failed')->count();

        if ($total > 0 && $delivered === $total) {
            $redemption->update([
                'status' => 'completed',
                'delivery_status' => 'delivered',
                'completed_at' => now(),
            ]);
        } elseif ($delivered > 0 && $failed > 0) {
            $redemption->update([
                'status' => 'partially_failed',
                'delivery_status' => 'failed',
            ]);
        } elseif ($failed > 0 && $delivered === 0) {
            $redemption->update([
                'status' => 'failed',
                'delivery_status' => 'failed',
            ]);
        }
    }

    protected function resolveRewards(RedeemCampaign $campaign, RedeemCode $code)
    {
        $codeRewards = $code->rewards()->orderBy('sort_order')->get();
        if ($codeRewards->isNotEmpty()) {
            return $codeRewards;
        }
        return $campaign->rewards()->orderBy('sort_order')->get();
    }

    protected function checkEligibility(User $user, RedeemCampaign $campaign, RedeemCode $code, ?int $characterId): array
    {
        $accountId = (int) ($user->account_id ?? 0);
        if ($accountId <= 0) {
            return ['ok' => false, 'message' => 'Your account is not linked to an in-game rAthena account.'];
        }

        if ($code->bound_account_id !== null && (int) $code->bound_account_id !== $accountId) {
            return ['ok' => false, 'message' => 'This redeem code is not eligible for your account.'];
        }

        if (!empty($campaign->allowed_account_ids) && !in_array($accountId, array_map('intval', $campaign->allowed_account_ids), true)) {
            return ['ok' => false, 'message' => 'This campaign is not available for your account.'];
        }

        // Campaign total cap
        if ($campaign->max_total_redemptions !== null) {
            $totalUsed = RedeemRedemption::where('campaign_id', $campaign->id)
                ->whereNotIn('status', ['cancelled'])
                ->count();
            if ($totalUsed >= $campaign->max_total_redemptions) {
                return ['ok' => false, 'message' => 'This redeem code is invalid, expired, or no longer available.'];
            }
        }

        // Per-code duplicate / per-account limit
        if ($campaign->one_per_account) {
            $alreadyCode = RedeemRedemption::where('redeem_code_id', $code->id)
                ->where('account_id', $accountId)
                ->whereNotIn('status', ['cancelled'])
                ->exists();
            if ($alreadyCode) {
                return ['ok' => false, 'message' => 'You have already redeemed this code on your account.'];
            }

            $campaignUses = RedeemRedemption::where('campaign_id', $campaign->id)
                ->where('account_id', $accountId)
                ->whereNotIn('status', ['cancelled'])
                ->count();
            if ($campaignUses >= max(1, (int) $campaign->max_per_account)) {
                return ['ok' => false, 'message' => 'You have reached the maximum redemptions allowed for this campaign.'];
            }
        }

        // Account age restrictions
        if ($campaign->account_age_rule === 'new_only' && $user->created_at && $user->created_at->lt(now()->subDays(7))) {
            return ['ok' => false, 'message' => 'This campaign is restricted to newly created accounts.'];
        }
        if ($campaign->account_age_rule === 'existing_only' && $user->created_at && $user->created_at->gt(now()->subDays(1))) {
            return ['ok' => false, 'message' => 'This campaign is restricted to existing accounts.'];
        }
        if ($campaign->account_created_after && $user->created_at && $user->created_at->lt($campaign->account_created_after)) {
            return ['ok' => false, 'message' => 'Your account does not meet the creation date requirement for this campaign.'];
        }
        if ($campaign->account_created_before && $user->created_at && $user->created_at->gt($campaign->account_created_before)) {
            return ['ok' => false, 'message' => 'Your account does not meet the creation date requirement for this campaign.'];
        }
        if ($campaign->min_account_age_days !== null && $user->created_at && $user->created_at->gt(now()->subDays($campaign->min_account_age_days))) {
            return ['ok' => false, 'message' => "Your account must be at least {$campaign->min_account_age_days} day(s) old to redeem this code."];
        }

        // Character validation if required or supplied
        $char = null;
        if ($characterId !== null) {
            $char = DB::connection('ragnarok')
                ->table($this->emulator->getTableName('char'))
                ->where('char_id', $characterId)
                ->where('account_id', $accountId)
                ->first();

            if (!$char) {
                return ['ok' => false, 'message' => 'Selected character does not belong to your account.'];
            }

            if ($campaign->one_per_character) {
                $charUsed = RedeemRedemption::where('campaign_id', $campaign->id)
                    ->where('character_id', $characterId)
                    ->whereNotIn('status', ['cancelled'])
                    ->exists();
                if ($charUsed) {
                    return ['ok' => false, 'message' => 'This character has already redeemed a code from this campaign.'];
                }
            }

            if (!empty($campaign->allowed_character_ids) && !in_array((int) $char->char_id, array_map('intval', $campaign->allowed_character_ids), true)) {
                return ['ok' => false, 'message' => 'Selected character is not eligible for this campaign.'];
            }

            if ($campaign->min_base_level !== null && (int) $char->base_level < $campaign->min_base_level) {
                return ['ok' => false, 'message' => "Character must be at least Base Level {$campaign->min_base_level}."];
            }

            if ($campaign->max_base_level !== null && (int) $char->base_level > $campaign->max_base_level) {
                return ['ok' => false, 'message' => "Character exceeds maximum Base Level {$campaign->max_base_level}."];
            }

            if (!empty($campaign->allowed_classes) && !in_array((int) $char->class, array_map('intval', $campaign->allowed_classes), true)) {
                return ['ok' => false, 'message' => 'Selected character class is not eligible for this campaign.'];
            }

            if (!empty($campaign->excluded_classes) && in_array((int) $char->class, array_map('intval', $campaign->excluded_classes), true)) {
                return ['ok' => false, 'message' => 'Selected character class is excluded from this campaign.'];
            }
        } elseif (
            $campaign->min_base_level !== null
            || $campaign->max_base_level !== null
            || !empty($campaign->allowed_classes)
            || !empty($campaign->excluded_classes)
            || !empty($campaign->allowed_character_ids)
            || $campaign->one_per_character
        ) {
            return ['ok' => false, 'message' => 'Please select an eligible character to redeem this code.'];
        }

        return ['ok' => true, 'character' => $char];
    }
}
