<?php

namespace Database\Seeders;

use App\Models\ForumBoard;
use App\Models\ForumCategory;
use App\Models\ReputationLevel;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Newcomer', 'slug' => 'newcomer', 'min_points' => 0, 'badge_color' => '#64748b', 'badge_icon' => 'sprout', 'badge_style' => 'subtle', 'sort_order' => 1],
            ['name' => 'Contributor', 'slug' => 'contributor', 'min_points' => 10, 'badge_color' => '#10b981', 'badge_icon' => 'sparkles', 'badge_style' => 'solid', 'sort_order' => 2],
            ['name' => 'Active Member', 'slug' => 'active-member', 'min_points' => 50, 'badge_color' => '#06b6d4', 'badge_icon' => 'shield', 'badge_style' => 'solid', 'sort_order' => 3],
            ['name' => 'Veteran', 'slug' => 'veteran', 'min_points' => 150, 'badge_color' => '#3b82f6', 'badge_icon' => 'swords', 'badge_style' => 'solid', 'sort_order' => 4],
            ['name' => 'Elite Member', 'slug' => 'elite-member', 'min_points' => 400, 'badge_color' => '#8b5cf6', 'badge_icon' => 'gem', 'badge_style' => 'glow', 'sort_order' => 5],
            ['name' => 'Community Legend', 'slug' => 'community-legend', 'min_points' => 1000, 'badge_color' => '#f59e0b', 'badge_icon' => 'crown', 'badge_style' => 'glow', 'sort_order' => 6],
        ];

        foreach ($levels as $lvl) {
            ReputationLevel::firstOrCreate(['slug' => $lvl['slug']], array_merge($lvl, ['is_active' => true]));
        }

        $categories = [
            [
                'name' => 'Realm & Official',
                'slug' => 'realm-official',
                'description' => 'Official announcements, patch notes, and server maintenance updates.',
                'icon' => 'megaphone',
                'sort_order' => 1,
                'boards' => [
                    ['name' => 'Announcements & Patch Notes', 'slug' => 'announcements', 'description' => 'Official server news, events, and changelog discussions.', 'icon' => 'bell', 'sort_order' => 1, 'allow_topics_roles' => ['admin', 'staff']],
                    ['name' => 'Server Events & Contests', 'slug' => 'server-events', 'description' => 'Community tournaments, WoE schedules, and seasonal contests.', 'icon' => 'trophy', 'sort_order' => 2, 'allow_topics_roles' => null],
                ],
            ],
            [
                'name' => 'Adventurer Tavern',
                'slug' => 'adventurer-tavern',
                'description' => 'General Ragnarok Online discussions, class guides, and party recruitment.',
                'icon' => 'message-square',
                'sort_order' => 2,
                'boards' => [
                    ['name' => 'General Discussion', 'slug' => 'general-discussion', 'description' => 'Chat about anything related to Rune-Midgard and our community.', 'icon' => 'messages-square', 'sort_order' => 1, 'allow_topics_roles' => null],
                    ['name' => 'Class Builds & Guides', 'slug' => 'class-guides', 'description' => 'Share equipment builds, MVP strategies, and leveling guides.', 'icon' => 'book-open', 'sort_order' => 2, 'allow_topics_roles' => null],
                    ['name' => 'Guild Recruitment & Diplomacy', 'slug' => 'guild-recruitment', 'description' => 'Recruit adventurers for WoE, Endless Tower, and instance runs.', 'icon' => 'flag', 'sort_order' => 3, 'allow_topics_roles' => null],
                ],
            ],
            [
                'name' => 'Trade & Support',
                'slug' => 'trade-support',
                'description' => 'Marketplace price checks, technical help, and player Q&A.',
                'icon' => 'life-buoy',
                'sort_order' => 3,
                'boards' => [
                    ['name' => 'Questions & Help', 'slug' => 'questions-help', 'description' => 'Ask the community for help with quests, client setup, or mechanics.', 'icon' => 'help-circle', 'sort_order' => 1, 'allow_topics_roles' => null],
                    ['name' => 'Market & Price Checks', 'slug' => 'market-trade', 'description' => 'Buy, sell, trade, and request item price checks from fellow players.', 'icon' => 'coins', 'sort_order' => 2, 'allow_topics_roles' => null],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $boards = $catData['boards'];
            unset($catData['boards']);

            $category = ForumCategory::firstOrCreate(
                ['slug' => $catData['slug']],
                array_merge($catData, ['is_active' => true])
            );

            foreach ($boards as $boardData) {
                ForumBoard::firstOrCreate(
                    ['slug' => $boardData['slug']],
                    array_merge($boardData, [
                        'category_id' => $category->id,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
