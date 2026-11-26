<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Models\RewardDefinition;
use App\Services\Ranking\BadgeService;
use Illuminate\Database\Seeder;

class RewardsBadgesSeeder extends Seeder
{
    /**
     * Seed Control Panel badge definitions, tiers, and milestone reward definitions
     * (AetherCP Control Panel database only — no Ragnarok game DB queries).
     */
    public function run(): void
    {
        app(BadgeService::class)->ensureDefaultDefinitionsExist();

        $firstMaxBadge = BadgeDefinition::where('auto_condition_type', 'first_max_base')->first();
        if ($firstMaxBadge) {
            RewardDefinition::firstOrCreate(
                ['name' => 'First to Max Level Grand Prize'],
                [
                    'category' => 'level',
                    'trigger_type' => 'badge',
                    'badge_id' => $firstMaxBadge->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 500000,
                    'policy' => 'one_time',
                    'message' => 'Congratulations on being the first to reach Maximum Base Level!',
                    'is_enabled' => true,
                ]
            );
        }

        $maxBaseBadge = BadgeDefinition::where('auto_condition_type', 'max_base')->first();
        if ($maxBaseBadge) {
            RewardDefinition::firstOrCreate(
                ['name' => 'Max Base Level Milestone'],
                [
                    'category' => 'level',
                    'trigger_type' => 'badge',
                    'badge_id' => $maxBaseBadge->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 100000,
                    'policy' => 'one_time',
                    'message' => 'Congratulations on reaching Maximum Base Level!',
                    'is_enabled' => true,
                ]
            );
        }

        $bronze5Pvp = BadgeTier::where('category', 'pvp')->where('tier_name', 'Bronze')->where('stars', 5)->first();
        if ($bronze5Pvp) {
            RewardDefinition::firstOrCreate(
                ['name' => 'PvP Bronze 5-Star Milestone'],
                [
                    'category' => 'pvp',
                    'trigger_type' => 'tier',
                    'badge_tier_id' => $bronze5Pvp->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 25000,
                    'policy' => 'one_time',
                    'message' => 'You reached Bronze 5 Stars in PvP!',
                    'is_enabled' => true,
                ]
            );
        }

        $bronze5Mvp = BadgeTier::where('category', 'mvp')->where('tier_name', 'Bronze')->where('stars', 5)->first();
        if ($bronze5Mvp) {
            RewardDefinition::firstOrCreate(
                ['name' => 'MVP Hunter Bronze 5-Star Milestone'],
                [
                    'category' => 'mvp',
                    'trigger_type' => 'tier',
                    'badge_tier_id' => $bronze5Mvp->id,
                    'reward_type' => 'zeny',
                    'zeny_amount' => 25000,
                    'policy' => 'one_time',
                    'message' => 'You reached Bronze 5 Stars in MVP Hunts!',
                    'is_enabled' => true,
                ]
            );
        }
    }
}
