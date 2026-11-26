<?php

namespace Database\Seeders;

use App\Models\BadgeDefinition;
use App\Models\BadgeTier;
use App\Models\ForumBoard;
use App\Models\ForumTopic;
use App\Models\RewardDefinition;
use App\Models\User;
use App\Services\Forum\ForumService;
use App\Services\Ranking\BadgeService;
use Illuminate\Database\Seeder;

class CommunityRewardsBadgesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Community Forum categories, boards, and reputation levels
        (new ForumSeeder())->run();

        // Seed a welcome announcement topic if no topics exist yet and an admin/user exists
        if (ForumTopic::count() === 0) {
            $author = User::where('role', 'admin')->first() ?? User::first();
            $announcementBoard = ForumBoard::where('slug', 'announcements')->first();
            $generalBoard = ForumBoard::where('slug', 'general-discussion')->first();

            if ($author && $announcementBoard) {
                app(ForumService::class)->createTopic($author, $announcementBoard, [
                    'title' => 'Welcome to the AetherCP Community Forums!',
                    'content' => "Welcome, adventurers of Rune-Midgard!\n\nOur new Community Forum is now live. Share class builds, recruit for War of Emperium, ask questions, and earn Community Reputation for helpful contributions.",
                ]);
            }

            if ($author && $generalBoard) {
                app(ForumService::class)->createTopic($author, $generalBoard, [
                    'title' => 'Introduce Yourself & Your Main Character',
                    'content' => "Drop a hello below! You can also head to Forum Identity Settings to showcase one of your in-game Ragnarok characters on your posts.",
                ]);
            }
        }

        // 2. Seed Badges (definitions + PvP/MVP star tiers)
        app(BadgeService::class)->ensureDefaultDefinitionsExist();

        // 3. Seed Rewards (milestone reward definitions linked to badges & tiers)
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
