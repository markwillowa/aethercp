<?php

namespace Tests\Feature;

use App\Models\PlayerInboxItem;
use App\Models\RedeemCampaign;
use App\Models\RedeemCode;
use App\Models\RedeemRedemption;
use App\Models\RedeemRewardDelivery;
use App\Models\User;
use App\Services\Redeem\RedeemCodeService;
use App\Services\Redeem\RedeemRedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RedeemCodeSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.connections.ragnarok', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        DB::purge('ragnarok');

        $ro = Schema::connection('ragnarok');

        $ro->create('login', function ($t) {
            $t->increments('account_id');
            $t->string('userid', 23);
            $t->string('user_pass', 64)->default('');
            $t->string('sex', 1)->default('M');
            $t->string('email', 100)->default('');
            $t->integer('group_id')->default(0);
        });

        $ro->create('char', function ($t) {
            $t->increments('char_id');
            $t->integer('account_id');
            $t->string('name', 30);
            $t->integer('class')->default(0);
            $t->integer('base_level')->default(1);
            $t->integer('job_level')->default(1);
            $t->integer('zeny')->default(0);
            $t->integer('online')->default(0);
        });

        $ro->create('acc_reg_num', function ($t) {
            $t->integer('account_id');
            $t->string('key', 32);
            $t->integer('index')->default(0);
            $t->integer('value')->default(0);
        });

        $ro->create('item_db', function ($t) {
            $t->integer('id')->primary();
            $t->string('name_aegis', 50)->default('');
            $t->string('name_english', 100)->default('');
        });

        DB::connection('ragnarok')->table('item_db')->insert([
            ['id' => 501, 'name_aegis' => 'Red_Potion', 'name_english' => 'Red Potion'],
            ['id' => 607, 'name_aegis' => 'Yggdrasil_Berry', 'name_english' => 'Yggdrasil Berry'],
        ]);
    }

    protected function makePlayer(int $accountId, string $username, int $baseLv = 50, int $class = 1, int $online = 0): array
    {
        $user = User::factory()->create(['username' => $username, 'account_id' => $accountId]);
        DB::connection('ragnarok')->table('login')->insert(['account_id' => $accountId, 'userid' => $username]);
        $charId = DB::connection('ragnarok')->table('char')->insertGetId([
            'account_id' => $accountId,
            'name' => $username . '_char',
            'class' => $class,
            'base_level' => $baseLv,
            'job_level' => 20,
            'zeny' => 1000,
            'online' => $online,
        ]);

        return [$user, (int) $charId];
    }

    public function test_admin_can_create_campaign_generate_single_bulk_shared_codes_and_export_csv(): void
    {
        $admin = User::factory()->create(['username' => 'redeemadmin', 'role' => 'admin']);
        /** @var RedeemCodeService $svc */
        $svc = app(RedeemCodeService::class);

        $campaign = $svc->createCampaign([
            'name' => 'Launch Event',
            'campaign_type' => 'launch_event',
            'status' => 'active',
        ], $admin);

        // Single manual
        $single = $svc->generateCodes($campaign, [
            'mode' => 'single_manual',
            'manual_code' => 'WELCOME-2027',
        ], $admin);
        $this->assertSame(['WELCOME-2027'], $single['plaintext']);

        // Shared code (5 max redemptions)
        $shared = $svc->generateCodes($campaign, [
            'mode' => 'shared',
            'manual_code' => 'AETHER-START',
            'max_redemptions' => 5,
        ], $admin);
        $this->assertSame(5, $shared['codes'][0]->max_redemptions);

        // Bulk generation + one-time CSV export
        $this->actingAs($admin)
            ->post(route('admin.redeem.codes.generate', $campaign->id), [
                'mode' => 'bulk',
                'prefix' => 'GIFT',
                'length' => 6,
                'count' => 5,
                'max_redemptions' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('redeem_codes', 7);

        $csvRes = $this->actingAs($admin)->get(route('admin.redeem.codes.export', $campaign->id));
        $csvRes->assertStatus(200);
        $this->assertStringContainsString('GIFT-', $csvRes->getContent());
    }

    public function test_redemption_delivers_cash_points_zeny_and_items_and_prevents_duplicates(): void
    {
        $admin = User::factory()->create(['username' => 'adm2', 'role' => 'admin']);
        [$player, $charId] = $this->makePlayer(3001, 'playerone', 99, 7);

        $svc = app(RedeemCodeService::class);
        $redempSvc = app(RedeemRedemptionService::class);

        $campaign = $svc->createCampaign(['name' => 'Multi Reward', 'status' => 'active', 'one_per_account' => true], $admin);
        $svc->addReward($campaign, ['reward_type' => 'cash_points', 'amount' => 500], $admin);
        $svc->addReward($campaign, ['reward_type' => 'zeny', 'amount' => 25000, 'delivery_scope' => 'character'], $admin);
        $svc->addReward($campaign, ['reward_type' => 'item', 'item_id' => 607, 'amount' => 3], $admin);

        $gen = $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'MULTI-2027'], $admin);

        // Case-insensitive redemption
        $res = $redempSvc->redeem($player, '  multi-2027  ', $charId, '127.0.0.1');
        $this->assertTrue($res['ok']);
        $this->assertSame('completed', $res['redemption']->status);

        // Verify Cash Points, Zeny, and Escrow Item
        $cp = DB::connection('ragnarok')->table('acc_reg_num')->where('account_id', 3001)->where('key', '#CASHPOINTS')->value('value');
        $this->assertSame(500, (int) $cp);

        $zeny = DB::connection('ragnarok')->table('char')->where('char_id', $charId)->value('zeny');
        $this->assertSame(26000, (int) $zeny);

        $this->assertSame(1, PlayerInboxItem::where('account_id', 3001)->where('item_id', 607)->where('quantity', 3)->count());

        // Duplicate attempt fails
        $dup = $redempSvc->redeem($player, 'MULTI-2027', $charId);
        $this->assertFalse($dup['ok']);
    }

    public function test_expiration_disabled_limits_binding_and_character_eligibility(): void
    {
        $admin = User::factory()->create(['username' => 'adm3', 'role' => 'admin']);
        [$p1, $c1] = $this->makePlayer(4001, 'lowbie', 10, 0);
        [$p2, $c2] = $this->makePlayer(4002, 'highbie', 99, 7);

        $svc = app(RedeemCodeService::class);
        $redempSvc = app(RedeemRedemptionService::class);

        $campaign = $svc->createCampaign([
            'name' => 'Restricted',
            'status' => 'active',
            'min_base_level' => 50,
            'allowed_classes' => [7],
            'one_per_account' => false,
            'max_per_account' => 5,
        ], $admin);
        $svc->addReward($campaign, ['reward_type' => 'cash_points', 'amount' => 100], $admin);

        // Expired code
        $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'EXP-1', 'expires_at' => now()->subHour()], $admin);
        $this->assertFalse($redempSvc->redeem($p2, 'EXP-1', $c2)['ok']);

        // Disabled code
        $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'DIS-1', 'status' => 'disabled'], $admin);
        $this->assertFalse($redempSvc->redeem($p2, 'DIS-1', $c2)['ok']);

        // Account bound code
        $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'BIND-1', 'bound_account_id' => 4002], $admin);
        $this->assertFalse($redempSvc->redeem($p1, 'BIND-1', $c1)['ok']);

        // Level/class ineligible
        $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'ELIG-1', 'max_redemptions' => 1], $admin);
        $this->assertFalse($redempSvc->redeem($p1, 'ELIG-1', $c1)['ok']);

        // Eligible player succeeds, then second player hits exhausted limit
        $this->assertTrue($redempSvc->redeem($p2, 'ELIG-1', $c2)['ok']);
        $this->assertFalse($redempSvc->redeem($p2, 'ELIG-1', $c2)['ok']);
    }

    public function test_partial_failure_when_online_and_admin_retry_succeeds(): void
    {
        $admin = User::factory()->create(['username' => 'adm4', 'role' => 'admin']);
        [$player, $charId] = $this->makePlayer(5001, 'onlinehero', 80, 7, 1); // online = 1

        $svc = app(RedeemCodeService::class);
        $redempSvc = app(RedeemRedemptionService::class);

        $campaign = $svc->createCampaign(['name' => 'Partial Fail', 'status' => 'active'], $admin);
        $svc->addReward($campaign, ['reward_type' => 'cash_points', 'amount' => 200], $admin);
        $svc->addReward($campaign, ['reward_type' => 'zeny', 'amount' => 10000, 'delivery_scope' => 'character'], $admin);

        $svc->generateCodes($campaign, ['mode' => 'single_manual', 'manual_code' => 'PARTIAL-1'], $admin);

        $res = $redempSvc->redeem($player, 'PARTIAL-1', $charId);
        $this->assertFalse($res['ok']);
        $this->assertTrue($res['partial']);
        $this->assertSame('partially_failed', $res['redemption']->status);

        // Cash points delivered, zeny failed
        $failedDelivery = RedeemRewardDelivery::where('redemption_id', $res['redemption']->id)->where('status', 'failed')->first();
        $this->assertNotNull($failedDelivery);
        $this->assertSame('zeny', $failedDelivery->reward_type);

        // Player logs out, admin retries failed delivery
        DB::connection('ragnarok')->table('char')->where('char_id', $charId)->update(['online' => 0]);

        $this->actingAs($admin)
            ->post(route('admin.redeem.deliveries.retry', $failedDelivery->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('completed', $res['redemption']->fresh()->status);
        $this->assertSame(11000, (int) DB::connection('ragnarok')->table('char')->where('char_id', $charId)->value('zeny'));
    }

    public function test_permissions_and_player_history_endpoint(): void
    {
        $staff = User::factory()->create(['username' => 'staff1', 'role' => 'staff']);
        $this->assertFalse($staff->hasRedeemPermission('manage_campaigns'));

        [$player, $charId] = $this->makePlayer(6001, 'histplayer', 50, 1);
        $this->actingAs($player)
            ->get(route('account.redeem.index'))
            ->assertStatus(200)
            ->assertSee('Redeem Code');
    }
}
