<?php

namespace Database\Seeders;

use App\Models\RedeemCampaign;
use App\Models\RedeemCode;
use App\Models\RedeemCodeReward;
use App\Services\Redeem\RedeemCodeService;
use Illuminate\Database\Seeder;

class RedeemCodeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var RedeemCodeService $svc */
        $svc = app(RedeemCodeService::class);

        $welcome = RedeemCampaign::firstOrCreate(
            ['slug' => 'welcome-reward-2027'],
            [
                'name' => 'Welcome Reward 2027',
                'description' => 'Starter welcome package for new and returning Rune-Midgard adventurers.',
                'campaign_type' => 'welcome_reward',
                'instructions' => 'Enter WELCOME-2027 to claim your starter Cash Points and supplies.',
                'status' => 'active',
                'is_public' => true,
                'one_per_account' => true,
                'max_per_account' => 1,
            ]
        );

        if ($welcome->rewards()->count() === 0) {
            RedeemCodeReward::create([
                'campaign_id' => $welcome->id,
                'reward_type' => 'cash_points',
                'amount' => 500,
                'delivery_scope' => 'account',
                'sort_order' => 1,
            ]);
            RedeemCodeReward::create([
                'campaign_id' => $welcome->id,
                'reward_type' => 'item',
                'amount' => 10,
                'delivery_scope' => 'account',
                'item_id' => 501,
                'item_name' => 'Red Potion',
                'is_bound' => true,
                'sort_order' => 2,
            ]);
        }

        $welcomeHash = $svc->hashCode('WELCOME-2027');
        RedeemCode::firstOrCreate(
            ['code_hash' => $welcomeHash],
            [
                'campaign_id' => $welcome->id,
                'code_prefix' => 'WELCOME',
                'code_hint' => $svc->makeHint('WELCOME-2027'),
                'code_type' => 'shared',
                'max_redemptions' => 500,
                'redemptions_count' => 0,
                'status' => 'active',
            ]
        );

        $launch = RedeemCampaign::firstOrCreate(
            ['slug' => 'aether-launch-event'],
            [
                'name' => 'Aether Launch Event',
                'description' => 'Grand launch celebration gift with Cash Points and Zeny.',
                'campaign_type' => 'launch_event',
                'instructions' => 'Enter AETHER-START and select an offline character to receive Zeny and Cash Points.',
                'status' => 'active',
                'is_public' => true,
                'one_per_account' => true,
                'max_per_account' => 1,
            ]
        );

        if ($launch->rewards()->count() === 0) {
            RedeemCodeReward::create([
                'campaign_id' => $launch->id,
                'reward_type' => 'cash_points',
                'amount' => 1000,
                'delivery_scope' => 'account',
                'sort_order' => 1,
            ]);
            RedeemCodeReward::create([
                'campaign_id' => $launch->id,
                'reward_type' => 'zeny',
                'amount' => 50000,
                'delivery_scope' => 'character',
                'sort_order' => 2,
            ]);
        }

        $launchHash = $svc->hashCode('AETHER-START');
        RedeemCode::firstOrCreate(
            ['code_hash' => $launchHash],
            [
                'campaign_id' => $launch->id,
                'code_prefix' => 'AETHER',
                'code_hint' => $svc->makeHint('AETHER-START'),
                'code_type' => 'shared',
                'max_redemptions' => 1000,
                'redemptions_count' => 0,
                'status' => 'active',
            ]
        );

        $this->command?->info('Seeded Redeem Code campaigns (WELCOME-2027, AETHER-START).');
    }
}
