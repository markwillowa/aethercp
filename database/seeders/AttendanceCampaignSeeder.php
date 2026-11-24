<?php

namespace Database\Seeders;

use App\Models\AttendanceCampaign;
use App\Models\AttendanceCampaignDay;
use App\Models\AttendanceCampaignItem;
use App\Services\Attendance\AttendanceScheduleBuilderService;
use Illuminate\Database\Seeder;

class AttendanceCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $builder = app(AttendanceScheduleBuilderService::class);

        // 1. 30-Day Monthly Attendance Campaign (Cumulative Mode, Inbox Delivery)
        $monthly = AttendanceCampaign::firstOrCreate(
            ['slug' => '30-day-monthly-attendance'],
            [
                'name' => '30-Day Monthly Attendance',
                'description' => 'Daily login attendance campaign for all adventurers. Missed days do not reset your progress! Claim your rewards daily and unlock exclusive weekly milestone gifts.',
                'duration_days' => 30,
                'progression_mode' => 'cumulative',
                'reset_rule' => 'none',
                'participation_scope' => 'account',
                'delivery_destination' => 'inbox',
                'allow_character_choice' => true,
                'require_in_game_login' => false,
                'repeatable' => true,
                'display_order' => 1,
                'min_base_level' => 1,
                'min_account_age_days' => 0,
                'daily_reset_hour' => 0,
                'timezone' => 'UTC',
                'status' => 'active',
            ]
        );

        if ($monthly->days()->count() === 0) {
            $builder->generateBaselineSchedule($monthly, [
                'base_zeny' => 10000,
                'zeny_step' => 2500,
                'default_item_id' => 501,
                'default_item_qty' => 10,
            ]);
        }

        // 2. 7-Day New Adventurer Welcome (Consecutive Streak Mode, Direct Character Inventory/Zeny)
        $welcome = AttendanceCampaign::firstOrCreate(
            ['slug' => '7-day-new-adventurer-welcome'],
            [
                'name' => '7-Day New Adventurer Welcome',
                'description' => 'Welcome to the server! Check in every day for 7 consecutive days to kickstart your journey with free pots, wings, cards, and starter Zeny.',
                'duration_days' => 7,
                'progression_mode' => 'consecutive',
                'reset_rule' => 'reset_to_start',
                'participation_scope' => 'account',
                'delivery_destination' => 'inbox',
                'allow_character_choice' => true,
                'require_in_game_login' => false,
                'repeatable' => false,
                'display_order' => 2,
                'min_base_level' => 1,
                'min_account_age_days' => 0,
                'daily_reset_hour' => 0,
                'timezone' => 'UTC',
                'status' => 'active',
            ]
        );

        if ($welcome->days()->count() === 0) {
            $welcomeSchedule = [
                1 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 10000],
                    ['reward_type' => 'item', 'item_id' => 501, 'item_name' => 'Red Potion', 'quantity' => 20],
                ],
                2 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 20000],
                    ['reward_type' => 'item', 'item_id' => 502, 'item_name' => 'Orange Potion', 'quantity' => 15],
                ],
                3 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 30000],
                    ['reward_type' => 'item', 'item_id' => 503, 'item_name' => 'White Potion', 'quantity' => 10],
                ],
                4 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 40000],
                    ['reward_type' => 'item', 'item_id' => 601, 'item_name' => 'Fly Wing', 'quantity' => 30],
                ],
                5 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 50000],
                    ['reward_type' => 'item', 'item_id' => 602, 'item_name' => 'Butterfly Wing', 'quantity' => 10],
                ],
                6 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 75000],
                    ['reward_type' => 'item', 'item_id' => 603, 'item_name' => 'Old Blue Box', 'quantity' => 1],
                ],
                7 => [
                    ['reward_type' => 'zeny', 'zeny_amount' => 150000],
                    ['reward_type' => 'item', 'item_id' => 616, 'item_name' => 'Old Card Album', 'quantity' => 1],
                    ['reward_type' => 'item', 'item_id' => 607, 'item_name' => 'Yggdrasil Berry', 'quantity' => 5],
                ],
            ];

            foreach ($welcomeSchedule as $dayNum => $rewards) {
                $isMilestone = ($dayNum === 7);
                $day = AttendanceCampaignDay::create([
                    'campaign_id' => $welcome->id,
                    'day_number' => $dayNum,
                    'title' => "Day {$dayNum}" . ($isMilestone ? ' (Grand Finale)' : ''),
                    'description' => $isMilestone ? '7-Day Welcome Milestone Package' : "Daily check-in reward for Day {$dayNum}",
                    'is_milestone' => $isMilestone,
                ]);

                foreach ($rewards as $r) {
                    AttendanceCampaignItem::create([
                        'campaign_day_id' => $day->id,
                        'reward_type' => $r['reward_type'],
                        'item_id' => $r['item_id'] ?? null,
                        'item_name' => $r['item_name'] ?? null,
                        'quantity' => $r['quantity'] ?? 1,
                        'zeny_amount' => $r['zeny_amount'] ?? 0,
                        'cashpoints_amount' => 0,
                        'refine' => 0,
                        'card0' => 0,
                        'card1' => 0,
                        'card2' => 0,
                        'card3' => 0,
                        'identified' => true,
                        'bound' => 0,
                    ]);
                }
            }
        }
    }
}
