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

        // 2. Seed Control Panel Badges & Milestone Rewards (AetherCP DB only)
        $this->call(RewardsBadgesSeeder::class);
    }
}
