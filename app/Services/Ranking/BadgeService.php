<?php

namespace App\Services\Ranking;

use App\Contracts\EmulatorInterface;
use App\Models\BadgeAssignment;
use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Models\RankingCharacterStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BadgeService
{
    public function __construct(
        protected EmulatorInterface $emulator,
        protected RankingEngineService $rankingEngine
    ) {}

    /**
     * Populate default configurable badge definitions and 5-star tiers if empty.
     */
    public function ensureDefaultDefinitionsExist(): void
    {
        if (BadgeDefinition::count() === 0) {
            $defaults = [
                [
                    'slug' => 'first-to-max-level',
                    'name' => 'First to Max Level',
                    'description' => 'First adventurer on the server to reach maximum base level.',
                    'category' => 'level',
                    'icon_type' => 'lucide',
                    'icon' => 'crown',
                    'color' => '#f59e0b',
                    'visual_style' => 'glow',
                    'priority' => 100,
                    'is_enabled' => true,
                    'is_permanent' => true,
                    'auto_condition_type' => 'first_max_base',
                ],
                [
                    'slug' => 'max-level',
                    'name' => 'Max Level',
                    'description' => 'Reached the server maximum base level.',
                    'category' => 'level',
                    'icon_type' => 'lucide',
                    'icon' => 'star',
                    'color' => '#06b6d4',
                    'visual_style' => 'solid',
                    'priority' => 80,
                    'is_enabled' => true,
                    'is_permanent' => true,
                    'auto_condition_type' => 'max_base',
                ],
                [
                    'slug' => 'first-to-max-job',
                    'name' => 'First to Max Job',
                    'description' => 'First adventurer to reach maximum job level.',
                    'category' => 'job',
                    'icon_type' => 'lucide',
                    'icon' => 'award',
                    'color' => '#8b5cf6',
                    'visual_style' => 'glow',
                    'priority' => 95,
                    'is_enabled' => true,
                    'is_permanent' => true,
                    'auto_condition_type' => 'first_max_job',
                ],
                [
                    'slug' => 'max-job-level',
                    'name' => 'Max Job Level',
                    'description' => 'Mastered the maximum job level for their class.',
                    'category' => 'job',
                    'icon_type' => 'lucide',
                    'icon' => 'sparkles',
                    'color' => '#3b82f6',
                    'visual_style' => 'solid',
                    'priority' => 75,
                    'is_enabled' => true,
                    'is_permanent' => true,
                    'auto_condition_type' => 'max_job',
                ],
                [
                    'slug' => 'top-1-guild',
                    'name' => 'Top 1 Guild',
                    'description' => 'Currently ranked #1 Guild on the server.',
                    'category' => 'guild',
                    'icon_type' => 'lucide',
                    'icon' => 'trophy',
                    'color' => '#f59e0b',
                    'visual_style' => 'glow',
                    'priority' => 100,
                    'is_enabled' => true,
                    'is_permanent' => false,
                    'auto_condition_type' => 'guild_rank_1',
                ],
                [
                    'slug' => 'top-2-guild',
                    'name' => 'Top 2 Guild',
                    'description' => 'Currently ranked #2 Guild on the server.',
                    'category' => 'guild',
                    'icon_type' => 'lucide',
                    'icon' => 'shield',
                    'color' => '#cbd5e1',
                    'visual_style' => 'solid',
                    'priority' => 90,
                    'is_enabled' => true,
                    'is_permanent' => false,
                    'auto_condition_type' => 'guild_rank_2',
                ],
                [
                    'slug' => 'top-3-guild',
                    'name' => 'Top 3 Guild',
                    'description' => 'Currently ranked #3 Guild on the server.',
                    'category' => 'guild',
                    'icon_type' => 'lucide',
                    'icon' => 'shield',
                    'color' => '#d97706',
                    'visual_style' => 'solid',
                    'priority' => 80,
                    'is_enabled' => true,
                    'is_permanent' => false,
                    'auto_condition_type' => 'guild_rank_3',
                ],
                [
                    'slug' => 'top-10-guild',
                    'name' => 'Top 4–10 Guild',
                    'description' => 'Ranked among the Top 10 Guilds on the server.',
                    'category' => 'guild',
                    'icon_type' => 'lucide',
                    'icon' => 'flag',
                    'color' => '#06b6d4',
                    'visual_style' => 'outline',
                    'priority' => 60,
                    'is_enabled' => true,
                    'is_permanent' => false,
                    'auto_condition_type' => 'guild_rank_top10',
                ],
            ];

            foreach ($defaults as $def) {
                BadgeDefinition::create($def);
            }
        }

        if (BadgeTier::count() === 0) {
            $tiers = [
                ['Bronze', 1, '#cd7f32', [10, 25, 50, 75, 100], [5, 15, 30, 50, 75]],
                ['Silver', 2, '#94a3b8', [150, 180, 210, 250, 280], [100, 125, 150, 175, 200]],
                ['Gold', 3, '#f59e0b', [300, 360, 420, 480, 540], [250, 300, 350, 400, 450]],
                ['Platinum', 4, '#22d3ee', [600, 680, 760, 840, 920], [500, 600, 700, 800, 900]],
                ['Diamond', 5, '#a855f7', [1000, 1250, 1500, 2000, 2500], [1000, 1250, 1500, 2000, 2500]],
            ];

            foreach ($tiers as [$name, $order, $color, $pvpKills, $mvpKills]) {
                for ($star = 1; $star <= 5; $star++) {
                    BadgeTier::create([
                        'category' => 'pvp',
                        'tier_name' => $name,
                        'tier_order' => $order,
                        'stars' => $star,
                        'min_kills' => $pvpKills[$star - 1],
                        'color' => $color,
                        'is_enabled' => true,
                    ]);

                    BadgeTier::create([
                        'category' => 'mvp',
                        'tier_name' => $name,
                        'tier_order' => $order,
                        'stars' => $star,
                        'min_kills' => $mvpKills[$star - 1],
                        'color' => $color,
                        'is_enabled' => true,
                    ]);
                }
            }
        }
    }

    /**
     * Evaluate and assign automatic level, job, PvP, and MVP badges for a character.
     */
    public function evaluateCharacterBadges(int $charId): array
    {
        $this->ensureDefaultDefinitionsExist();
        $awarded = [];

        $char = DB::connection('ragnarok')
            ->table($this->emulator->getTableName('char'))
            ->where('char_id', $charId)
            ->first();

        if (!$char) {
            return $awarded;
        }

        $cfg = $this->rankingEngine->getConfig();
        $stat = RankingCharacterStat::where('char_id', $charId)->first();

        // 1. Level & Job Definition Badges
        $defs = BadgeDefinition::where('is_enabled', true)
            ->whereIn('auto_condition_type', ['first_max_base', 'max_base', 'first_max_job', 'max_job'])
            ->get();

        $firstMaxBaseCharId = RankingCharacterStat::whereNotNull('max_base_reached_at')
            ->orderBy('max_base_reached_at')
            ->value('char_id');

        $firstMaxJobCharId = RankingCharacterStat::whereNotNull('max_job_reached_at')
            ->orderBy('max_job_reached_at')
            ->value('char_id');

        foreach ($defs as $def) {
            $qualifies = match ($def->auto_condition_type) {
                'first_max_base' => ((int) $char->base_level >= $cfg['max_base_level']) && ((int) $firstMaxBaseCharId === $charId),
                'max_base' => (int) $char->base_level >= $cfg['max_base_level'],
                'first_max_job' => ((int) $char->job_level >= $cfg['max_job_level']) && ((int) $firstMaxJobCharId === $charId),
                'max_job' => (int) $char->job_level >= $cfg['max_job_level'],
                default => false,
            };

            if ($qualifies) {
                $assignment = $this->assignDefinitionBadge($def, 'character', $charId);
                if ($assignment) {
                    $awarded[] = $assignment;
                }
            }
        }

        // 2. PvP Tier Badge
        if ($stat && $stat->pvp_kills > 0) {
            $tier = BadgeTier::where('category', 'pvp')
                ->where('is_enabled', true)
                ->where('min_kills', '<=', $stat->pvp_kills)
                ->orderByDesc('tier_order')
                ->orderByDesc('stars')
                ->first();

            if ($tier) {
                $assignment = $this->assignTierBadge($tier, $charId);
                if ($assignment) {
                    $awarded[] = $assignment;
                }
            }
        }

        // 3. MVP Tier Badge
        if ($stat && $stat->mvp_kills > 0) {
            $tier = BadgeTier::where('category', 'mvp')
                ->where('is_enabled', true)
                ->where('min_kills', '<=', $stat->mvp_kills)
                ->orderByDesc('tier_order')
                ->orderByDesc('stars')
                ->first();

            if ($tier) {
                $assignment = $this->assignTierBadge($tier, $charId);
                if ($assignment) {
                    $awarded[] = $assignment;
                }
            }
        }

        return $awarded;
    }

    /**
     * Evaluate and update ranking-based Guild badges (Top 1, Top 2, Top 3, Top 4-10).
     */
    public function evaluateGuildBadges(): void
    {
        $this->ensureDefaultDefinitionsExist();

        $guildBoard = $this->rankingEngine->getLeaderboard('guild', 50, 1);
        $items = $guildBoard['items'] ?? [];

        $rankMap = [];
        foreach ($items as $row) {
            $rank = (int) $row['rank'];
            $gid = (int) $row['guild_id'];
            if ($rank === 1) {
                $rankMap[$gid] = 'guild_rank_1';
            } elseif ($rank === 2) {
                $rankMap[$gid] = 'guild_rank_2';
            } elseif ($rank === 3) {
                $rankMap[$gid] = 'guild_rank_3';
            } elseif ($rank <= 10) {
                $rankMap[$gid] = 'guild_rank_top10';
            }
        }

        $guildDefs = BadgeDefinition::where('is_enabled', true)
            ->whereIn('auto_condition_type', ['guild_rank_1', 'guild_rank_2', 'guild_rank_3', 'guild_rank_top10'])
            ->get()
            ->keyBy('auto_condition_type');

        // Deactivate non-matching active guild ranking badges while preserving historical records
        $activeGuildAssignments = BadgeAssignment::where('target_type', 'guild')
            ->where('category', 'guild')
            ->where('assigned_by', 'system')
            ->where('is_active', true)
            ->get();

        foreach ($activeGuildAssignments as $active) {
            $expectedCond = $rankMap[$active->target_id] ?? null;
            $expectedDefId = $expectedCond && $guildDefs->has($expectedCond) ? $guildDefs->get($expectedCond)->id : null;

            if ($active->badge_id !== $expectedDefId) {
                $active->is_active = false;
                $active->revoked_at = now();
                $active->save();
            }
        }

        // Award missing active guild badges
        foreach ($rankMap as $gid => $condType) {
            if ($guildDefs->has($condType)) {
                $this->assignDefinitionBadge($guildDefs->get($condType), 'guild', $gid);
            }
        }
    }

    /**
     * Recalculate all character and guild badges.
     */
    public function recalculateAll(): array
    {
        $this->ensureDefaultDefinitionsExist();

        $charIds = DB::connection('ragnarok')
            ->table($this->emulator->getTableName('char'))
            ->pluck('char_id')
            ->all();

        $awardedCount = 0;
        foreach ($charIds as $cid) {
            $awardedCount += count($this->evaluateCharacterBadges((int) $cid));
        }

        $this->evaluateGuildBadges();
        $this->rankingEngine->invalidateCache();

        return [
            'characters_checked' => count($charIds),
            'new_badges_awarded' => $awardedCount,
        ];
    }

    /**
     * Batch fetch active badges for a list of character or guild IDs.
     */
    public function getActiveBadgesForTargets(string $targetType, array $targetIds): array
    {
        if (empty($targetIds) || !Schema::hasTable('badge_assignments')) {
            return [];
        }

        $assignments = BadgeAssignment::with('definition')
            ->where('target_type', $targetType)
            ->whereIn('target_id', $targetIds)
            ->where('is_active', true)
            ->orderByDesc('awarded_at')
            ->get();

        $grouped = [];
        foreach ($assignments as $a) {
            $grouped[$a->target_id][] = [
                'id' => $a->id,
                'badge_id' => $a->badge_id,
                'badge_tier_id' => $a->badge_tier_id,
                'category' => $a->category,
                'label' => $a->label,
                'stars' => $a->stars,
                'color' => $a->color,
                'icon_type' => $a->definition?->icon_type ?? 'lucide',
                'icon' => $a->definition?->icon ?? ($a->stars ? 'star' : 'award'),
                'icon_url' => $a->definition?->icon_url,
                'visual_style' => $a->definition?->visual_style ?? 'solid',
                'awarded_at' => $a->awarded_at?->format('M d, Y'),
            ];
        }

        return $grouped;
    }

    public function assignDefinitionBadge(BadgeDefinition $def, string $targetType, int $targetId, string $assignedBy = 'system', ?int $adminUserId = null, ?string $notes = null): ?BadgeAssignment
    {
        $exists = BadgeAssignment::where('badge_id', $def->id)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('is_active', true)
            ->exists();

        if ($exists) {
            return null;
        }

        $assignment = BadgeAssignment::create([
            'badge_id' => $def->id,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'category' => $def->category,
            'label' => $def->name,
            'color' => $def->color,
            'is_active' => true,
            'is_permanent' => $def->is_permanent,
            'assigned_by' => $assignedBy,
            'assigned_by_user_id' => $adminUserId,
            'notes' => $notes,
            'awarded_at' => now(),
        ]);

        app(RewardService::class)->evaluateAndIssueForBadgeAssignment($assignment);

        return $assignment;
    }

    protected function assignTierBadge(BadgeTier $tier, int $charId): ?BadgeAssignment
    {
        $alreadyActive = BadgeAssignment::where('badge_tier_id', $tier->id)
            ->where('target_type', 'character')
            ->where('target_id', $charId)
            ->where('is_active', true)
            ->exists();

        if ($alreadyActive) {
            return null;
        }

        // Deactivate lower/older active tier in the same category while keeping historical record
        BadgeAssignment::where('target_type', 'character')
            ->where('target_id', $charId)
            ->where('category', $tier->category)
            ->whereNotNull('badge_tier_id')
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'revoked_at' => now(),
            ]);

        $assignment = BadgeAssignment::create([
            'badge_tier_id' => $tier->id,
            'target_type' => 'character',
            'target_id' => $charId,
            'category' => $tier->category,
            'label' => $tier->formatted_label,
            'stars' => $tier->stars,
            'color' => $tier->color,
            'is_active' => true,
            'is_permanent' => true,
            'assigned_by' => 'system',
            'awarded_at' => now(),
        ]);

        app(RewardService::class)->evaluateAndIssueForBadgeAssignment($assignment);

        return $assignment;
    }
}
