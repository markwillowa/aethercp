<?php

namespace App\Services\Forum;

use App\Models\ForumUserProfile;
use App\Models\ReputationAuditLog;
use App\Models\ReputationLevel;
use App\Models\ReputationTransaction;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReputationService
{
    public const EVENT_POSITIVE_REACTION = 'positive_reaction';
    public const EVENT_ACCEPTED_ANSWER = 'accepted_answer';
    public const EVENT_APPROVED_GUIDE = 'approved_guide';
    public const EVENT_MODERATOR_RECOGNITION = 'moderator_recognition';
    public const EVENT_MANUAL_ADJUSTMENT = 'manual_adjustment';

    public function seedDefaultLevelsIfEmpty(): void
    {
        if (ReputationLevel::count() > 0) {
            return;
        }

        $defaults = [
            ['name' => 'Newcomer', 'slug' => 'newcomer', 'min_points' => 0, 'badge_color' => '#64748b', 'badge_icon' => 'sprout', 'badge_style' => 'subtle', 'sort_order' => 1],
            ['name' => 'Contributor', 'slug' => 'contributor', 'min_points' => 10, 'badge_color' => '#10b981', 'badge_icon' => 'sparkles', 'badge_style' => 'solid', 'sort_order' => 2],
            ['name' => 'Active Member', 'slug' => 'active-member', 'min_points' => 50, 'badge_color' => '#06b6d4', 'badge_icon' => 'shield', 'badge_style' => 'solid', 'sort_order' => 3],
            ['name' => 'Veteran', 'slug' => 'veteran', 'min_points' => 150, 'badge_color' => '#3b82f6', 'badge_icon' => 'swords', 'badge_style' => 'solid', 'sort_order' => 4],
            ['name' => 'Elite Member', 'slug' => 'elite-member', 'min_points' => 400, 'badge_color' => '#8b5cf6', 'badge_icon' => 'gem', 'badge_style' => 'glow', 'sort_order' => 5],
            ['name' => 'Community Legend', 'slug' => 'community-legend', 'min_points' => 1000, 'badge_color' => '#f59e0b', 'badge_icon' => 'crown', 'badge_style' => 'glow', 'sort_order' => 6],
        ];

        foreach ($defaults as $lvl) {
            ReputationLevel::firstOrCreate(['slug' => $lvl['slug']], array_merge($lvl, ['is_active' => true]));
        }

        Cache::forget('forum.reputation_levels');
        Cache::forget('forum.reputation_levels.v2');
    }

    public function getSettings(): array
    {
        $enabledEventsRaw = SiteSetting::get('forum_rep_enabled_events', null);
        $enabledEvents = $enabledEventsRaw !== null
            ? (json_decode((string) $enabledEventsRaw, true) ?: [])
            : [
                self::EVENT_POSITIVE_REACTION,
                self::EVENT_ACCEPTED_ANSWER,
                self::EVENT_APPROVED_GUIDE,
                self::EVENT_MODERATOR_RECOGNITION,
            ];

        return [
            'points_positive_reaction' => (int) SiteSetting::get('forum_rep_positive_reaction', 1),
            'points_accepted_answer' => (int) SiteSetting::get('forum_rep_accepted_answer', 10),
            'points_approved_guide' => (int) SiteSetting::get('forum_rep_approved_guide', 5),
            'points_moderator_recognition' => (int) SiteSetting::get('forum_rep_moderator_recognition', 15),
            'enabled_events' => $enabledEvents,
            'reversal_policy' => (string) SiteSetting::get('forum_rep_reversal_policy', 'reverse_all'),
            'leaderboard_visible' => (bool) SiteSetting::get('forum_rep_leaderboard_visible', 1),
        ];
    }

    public function updateSettings(array $data): void
    {
        SiteSetting::set('forum_rep_positive_reaction', (string) ((int) ($data['points_positive_reaction'] ?? 1)));
        SiteSetting::set('forum_rep_accepted_answer', (string) ((int) ($data['points_accepted_answer'] ?? 10)));
        SiteSetting::set('forum_rep_approved_guide', (string) ((int) ($data['points_approved_guide'] ?? 5)));
        SiteSetting::set('forum_rep_moderator_recognition', (string) ((int) ($data['points_moderator_recognition'] ?? 15)));
        SiteSetting::set('forum_rep_enabled_events', json_encode(array_values($data['enabled_events'] ?? [])));
        SiteSetting::set('forum_rep_reversal_policy', (string) ($data['reversal_policy'] ?? 'reverse_all'));
        SiteSetting::set('forum_rep_leaderboard_visible', !empty($data['leaderboard_visible']) ? '1' : '0');
    }

    public function getLevels(): \Illuminate\Support\Collection
    {
        $this->seedDefaultLevelsIfEmpty();

        $rows = Cache::remember('forum.reputation_levels.v2', 300, function () {
            return ReputationLevel::where('is_active', true)
                ->orderBy('min_points')
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        });

        return ReputationLevel::hydrate(is_array($rows) ? $rows : []);
    }

    public function getLevelForPoints(int $points): array
    {
        $levels = $this->getLevels();
        $matched = $levels->first();

        foreach ($levels as $level) {
            if ($points >= $level->min_points) {
                $matched = $level;
            }
        }

        if (!$matched) {
            return [
                'name' => 'Newcomer',
                'slug' => 'newcomer',
                'min_points' => 0,
                'badge_color' => '#64748b',
                'badge_icon' => 'sprout',
                'badge_style' => 'subtle',
            ];
        }

        return [
            'id' => $matched->id,
            'name' => $matched->name,
            'slug' => $matched->slug,
            'min_points' => $matched->min_points,
            'badge_color' => $matched->badge_color,
            'badge_icon' => $matched->badge_icon,
            'badge_style' => $matched->badge_style,
        ];
    }

    public function getPointsForEvent(string $eventType): int
    {
        $settings = $this->getSettings();

        return match ($eventType) {
            self::EVENT_POSITIVE_REACTION => $settings['points_positive_reaction'],
            self::EVENT_ACCEPTED_ANSWER => $settings['points_accepted_answer'],
            self::EVENT_APPROVED_GUIDE => $settings['points_approved_guide'],
            self::EVENT_MODERATOR_RECOGNITION => $settings['points_moderator_recognition'],
            default => 0,
        };
    }

    public function isEventEnabled(string $eventType): bool
    {
        if ($eventType === self::EVENT_MANUAL_ADJUSTMENT) {
            return true;
        }

        $settings = $this->getSettings();
        return in_array($eventType, $settings['enabled_events'], true);
    }

    public function award(
        User $recipient,
        ?User $source,
        string $eventType,
        string $eventReference,
        string $description,
        ?int $topicId = null,
        ?int $postId = null,
        ?int $overridePoints = null
    ): ?ReputationTransaction {
        // Users cannot award themselves reputation (except admin manual adjustments if explicitly audited)
        if ($source && $source->id === $recipient->id && $eventType !== self::EVENT_MANUAL_ADJUSTMENT) {
            return null;
        }

        if (!$this->isEventEnabled($eventType)) {
            return null;
        }

        $points = $overridePoints ?? $this->getPointsForEvent($eventType);
        if ($points === 0) {
            return null;
        }

        return DB::transaction(function () use ($recipient, $source, $eventType, $eventReference, $description, $topicId, $postId, $points) {
            // Prevent duplicate transactions using unique event references
            if (ReputationTransaction::where('event_reference', $eventReference)->exists()) {
                return null;
            }

            $tx = ReputationTransaction::create([
                'recipient_user_id' => $recipient->id,
                'source_user_id' => $source?->id,
                'event_type' => $eventType,
                'points' => $points,
                'topic_id' => $topicId,
                'post_id' => $postId,
                'event_reference' => $eventReference,
                'description' => Str::limit($description, 250),
                'is_reversal' => false,
            ]);

            $this->recalculateUserTotal($recipient->id);
            $this->invalidateUserCache($recipient->id);

            return $tx;
        });
    }

    public function reverseTransaction(ReputationTransaction $original, string $description): ?ReputationTransaction
    {
        return DB::transaction(function () use ($original, $description) {
            $alreadyReversed = ReputationTransaction::where('reversed_transaction_id', $original->id)->exists();
            if ($alreadyReversed || $original->is_reversal) {
                return null;
            }

            $revRef = 'rev:' . $original->event_reference;
            if (ReputationTransaction::where('event_reference', $revRef)->exists()) {
                return null;
            }

            $tx = ReputationTransaction::create([
                'recipient_user_id' => $original->recipient_user_id,
                'source_user_id' => $original->source_user_id,
                'event_type' => $original->event_type,
                'points' => -$original->points,
                'topic_id' => $original->topic_id,
                'post_id' => $original->post_id,
                'event_reference' => $revRef,
                'description' => Str::limit($description, 250),
                'is_reversal' => true,
                'reversed_transaction_id' => $original->id,
            ]);

            $this->recalculateUserTotal($original->recipient_user_id);
            $this->invalidateUserCache($original->recipient_user_id);

            return $tx;
        });
    }

    public function reverseReactionAward(int $postId, int $sourceUserId, string $type = 'like'): ?ReputationTransaction
    {
        $latestAward = ReputationTransaction::where('post_id', $postId)
            ->where('source_user_id', $sourceUserId)
            ->where('event_type', self::EVENT_POSITIVE_REACTION)
            ->where('is_reversal', false)
            ->latest('id')
            ->first();

        if (!$latestAward) {
            return null;
        }

        return $this->reverseTransaction($latestAward, 'Reaction removed on post #' . $postId);
    }

    public function awardReactionCycle(User $recipient, User $source, int $postId, int $topicId, string $type = 'like'): ?ReputationTransaction
    {
        if ($recipient->id === $source->id) {
            return null;
        }

        // Check if there is already an unreversed positive reaction from this user on this post
        $lastTx = ReputationTransaction::where('post_id', $postId)
            ->where('source_user_id', $source->id)
            ->where('event_type', self::EVENT_POSITIVE_REACTION)
            ->latest('id')
            ->first();

        if ($lastTx && !$lastTx->is_reversal) {
            // Already awarded and not reversed — prevent farming
            return null;
        }

        $cycleCount = ReputationTransaction::where('post_id', $postId)
            ->where('source_user_id', $source->id)
            ->where('event_type', self::EVENT_POSITIVE_REACTION)
            ->where('is_reversal', false)
            ->count();

        $eventRef = $cycleCount === 0
            ? "reaction:{$postId}:{$source->id}:{$type}"
            : "reaction:{$postId}:{$source->id}:{$type}:c" . ($cycleCount + 1);

        return $this->award(
            $recipient,
            $source,
            self::EVENT_POSITIVE_REACTION,
            $eventRef,
            "Received a positive reaction from {$source->username}",
            $topicId,
            $postId
        );
    }

    public function handleContentDeletion(?int $topicId = null, ?int $postId = null): void
    {
        $settings = $this->getSettings();
        if ($settings['reversal_policy'] !== 'reverse_all') {
            return;
        }

        $query = ReputationTransaction::where('is_reversal', false);
        if ($postId !== null) {
            $query->where('post_id', $postId);
        } elseif ($topicId !== null) {
            $query->where('topic_id', $topicId);
        } else {
            return;
        }

        $transactions = $query->get();
        foreach ($transactions as $tx) {
            $this->reverseTransaction($tx, 'Reversed due to content deletion');
        }
    }

    public function manualAdjust(User $admin, User $recipient, int $pointsDelta, string $reason): ReputationAuditLog
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('A reason is required for manual reputation adjustments.');
        }

        if ($pointsDelta === 0) {
            throw new \InvalidArgumentException('Reputation adjustment cannot be zero.');
        }

        return DB::transaction(function () use ($admin, $recipient, $pointsDelta, $reason) {
            $profile = ForumUserProfile::forUser($recipient->id);
            $previousTotal = (int) $profile->reputation_points;

            $eventRef = 'manual:' . $recipient->id . ':' . Str::uuid();
            $tx = ReputationTransaction::create([
                'recipient_user_id' => $recipient->id,
                'source_user_id' => $admin->id,
                'event_type' => self::EVENT_MANUAL_ADJUSTMENT,
                'points' => $pointsDelta,
                'event_reference' => $eventRef,
                'description' => "Moderator adjustment: " . Str::limit(trim($reason), 200),
                'is_reversal' => false,
            ]);

            $newTotal = $this->recalculateUserTotal($recipient->id);
            $this->invalidateUserCache($recipient->id);

            return ReputationAuditLog::create([
                'admin_user_id' => $admin->id,
                'recipient_user_id' => $recipient->id,
                'points_delta' => $pointsDelta,
                'previous_total' => $previousTotal,
                'new_total' => $newTotal,
                'reason' => trim($reason),
                'transaction_id' => $tx->id,
            ]);
        });
    }

    public function recalculateUserTotal(int $userId): int
    {
        $total = (int) ReputationTransaction::where('recipient_user_id', $userId)->sum('points');
        $profile = ForumUserProfile::forUser($userId);
        $profile->reputation_points = $total;
        $profile->save();

        return $total;
    }

    public function getUserTotal(int $userId): int
    {
        return Cache::remember("forum.rep.total.{$userId}", 300, function () use ($userId) {
            $profile = ForumUserProfile::where('user_id', $userId)->first();
            return $profile ? (int) $profile->reputation_points : 0;
        });
    }

    public function invalidateUserCache(int $userId): void
    {
        Cache::forget("forum.rep.total.{$userId}");
    }

    public function getLeaderboard(string $period = 'all', int $perPage = 20): LengthAwarePaginator
    {
        if ($period === 'week' || $period === 'month') {
            $since = $period === 'week' ? now()->startOfWeek() : now()->startOfMonth();

            $paginator = ReputationTransaction::query()
                ->select('recipient_user_id', DB::raw('SUM(points) as period_points'), DB::raw('COUNT(*) as contribution_count'))
                ->where('created_at', '>=', $since)
                ->groupBy('recipient_user_id')
                ->havingRaw('SUM(points) > 0')
                ->orderByDesc('period_points')
                ->paginate($perPage)
                ->withQueryString();

            $userIds = $paginator->getCollection()->pluck('recipient_user_id')->all();
            $users = User::with('forumProfile')->whereIn('id', $userIds)->get()->keyBy('id');

            $paginator->getCollection()->transform(function ($row) use ($users) {
                $user = $users->get($row->recipient_user_id);
                $totalPoints = $user?->forumProfile?->reputation_points ?? (int) $row->period_points;

                return (object) [
                    'user' => $user,
                    'points' => (int) $row->period_points,
                    'total_points' => (int) $totalPoints,
                    'contribution_count' => (int) $row->contribution_count,
                    'level' => $this->getLevelForPoints((int) $totalPoints),
                ];
            });

            return $paginator;
        }

        $paginator = User::query()
            ->leftJoin('forum_user_profiles', 'users.id', '=', 'forum_user_profiles.user_id')
            ->select('users.*', DB::raw('COALESCE(forum_user_profiles.reputation_points, 0) as rep_points'))
            ->with('forumProfile')
            ->orderByDesc('rep_points')
            ->orderBy('users.id')
            ->paginate($perPage)
            ->withQueryString();

        $userIds = $paginator->getCollection()->pluck('id')->all();
        $txCounts = ReputationTransaction::query()
            ->select('recipient_user_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('recipient_user_id', $userIds)
            ->where('is_reversal', false)
            ->groupBy('recipient_user_id')
            ->pluck('cnt', 'recipient_user_id');

        $paginator->getCollection()->transform(function ($user) use ($txCounts) {
            $points = (int) ($user->rep_points ?? 0);
            return (object) [
                'user' => $user,
                'points' => $points,
                'total_points' => $points,
                'contribution_count' => (int) ($txCounts->get($user->id) ?? 0),
                'level' => $this->getLevelForPoints($points),
            ];
        });

        return $paginator;
    }
}
