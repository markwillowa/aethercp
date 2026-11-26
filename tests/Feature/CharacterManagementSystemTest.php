<?php

namespace Tests\Feature;

use App\Models\CharacterDeletionRequest;
use App\Models\CharacterLock;
use App\Models\CharacterServiceLog;
use App\Models\CharacterServiceTransaction;
use App\Models\PlayerInboxItem;
use App\Models\User;
use App\Services\Character\CharacterManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CharacterManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $player;
    private User $otherPlayer;
    private User $admin;
    private CharacterManagementService $service;

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
            $t->integer('state')->default(0);
        });

        $ro->create('char', function ($t) {
            $t->increments('char_id');
            $t->integer('account_id');
            $t->integer('char_num')->default(0);
            $t->string('name', 30);
            $t->integer('class')->default(4013);
            $t->integer('base_level')->default(99);
            $t->integer('job_level')->default(70);
            $t->integer('base_exp')->default(0);
            $t->integer('job_exp')->default(0);
            $t->integer('zeny')->default(100000);
            $t->integer('str')->default(50);
            $t->integer('agi')->default(50);
            $t->integer('vit')->default(30);
            $t->integer('int')->default(10);
            $t->integer('dex')->default(40);
            $t->integer('luk')->default(1);
            $t->integer('status_point')->default(5);
            $t->integer('skill_point')->default(0);
            $t->integer('hair')->default(1);
            $t->integer('hair_color')->default(1);
            $t->integer('clothes_color')->default(0);
            $t->integer('guild_id')->default(0);
            $t->integer('party_id')->default(0);
            $t->string('last_map', 32)->default('prontera');
            $t->integer('last_x')->default(156);
            $t->integer('last_y')->default(191);
            $t->string('save_map', 32)->default('prontera');
            $t->integer('save_x')->default(156);
            $t->integer('save_y')->default(191);
            $t->integer('online')->default(0);
            $t->integer('delete_date')->default(0);
            $t->string('sex', 1)->default('M');
            $t->string('last_login')->nullable();
        });

        $ro->create('acc_reg_num', function ($t) {
            $t->integer('account_id');
            $t->string('key', 32);
            $t->integer('index')->default(0);
            $t->integer('value')->default(0);
        });

        $ro->create('guild', function ($t) {
            $t->increments('guild_id');
            $t->string('name', 24);
        });

        $ro->create('party', function ($t) {
            $t->increments('party_id');
            $t->string('name', 24);
        });

        $ro->create('skill', function ($t) {
            $t->integer('char_id');
            $t->integer('id');
            $t->integer('lv')->default(0);
            $t->integer('flag')->default(0);
        });

        $ro->create('inventory', function ($t) {
            $t->increments('id');
            $t->integer('char_id');
            $t->integer('nameid');
            $t->integer('amount')->default(1);
            $t->integer('equip')->default(0);
            $t->integer('identify')->default(1);
            $t->integer('refine')->default(0);
            $t->integer('attribute')->default(0);
            $t->integer('bound')->default(0);
        });

        $ro->create('cart_inventory', function ($t) {
            $t->increments('id');
            $t->integer('char_id');
            $t->integer('nameid');
            $t->integer('amount')->default(1);
            $t->integer('equip')->default(0);
            $t->integer('refine')->default(0);
            $t->integer('bound')->default(0);
        });

        $ro->create('storage', function ($t) {
            $t->increments('id');
            $t->integer('account_id');
            $t->integer('nameid');
            $t->integer('amount')->default(1);
            $t->integer('equip')->default(0);
            $t->integer('refine')->default(0);
            $t->integer('bound')->default(0);
        });

        $ro->create('item_db', function ($t) {
            $t->integer('id')->primary();
            $t->string('name_aegis', 64);
            $t->string('name_english', 100);
            $t->string('type', 32)->default('Healing');
        });

        DB::connection('ragnarok')->table('login')->insert([
            ['account_id' => 2000001, 'userid' => 'playerone', 'sex' => 'M', 'group_id' => 0],
            ['account_id' => 2000002, 'userid' => 'playertwo', 'sex' => 'F', 'group_id' => 0],
        ]);

        DB::connection('ragnarok')->table('char')->insert([
            ['char_id' => 150001, 'account_id' => 2000001, 'char_num' => 0, 'name' => 'Freynix', 'online' => 0, 'sex' => 'M'],
            ['char_id' => 150002, 'account_id' => 2000001, 'char_num' => 1, 'name' => 'Approval', 'online' => 0, 'sex' => 'M'],
            ['char_id' => 150003, 'account_id' => 2000002, 'char_num' => 0, 'name' => 'OtherHero', 'online' => 0, 'sex' => 'F'],
        ]);

        DB::connection('ragnarok')->table('acc_reg_num')->insert([
            'account_id' => 2000001,
            'key' => '#CASHPOINTS',
            'index' => 0,
            'value' => 5000,
        ]);

        DB::connection('ragnarok')->table('skill')->insert([
            ['char_id' => 150001, 'id' => 1, 'lv' => 9, 'flag' => 0],
            ['char_id' => 150001, 'id' => 50, 'lv' => 10, 'flag' => 0],
        ]);

        DB::connection('ragnarok')->table('item_db')->insert([
            'id' => 501,
            'name_aegis' => 'Red_Potion',
            'name_english' => 'Red Potion',
            'type' => 'Healing',
        ]);

        $this->player = User::create([
            'account_id' => 2000001,
            'username' => 'playerone',
            'name' => 'Player One',
            'email' => 'p1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->otherPlayer = User::create([
            'account_id' => 2000002,
            'username' => 'playertwo',
            'name' => 'Player Two',
            'email' => 'p2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->admin = User::create([
            'account_id' => 2000003,
            'username' => 'adminuser',
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->service = app(CharacterManagementService::class);
    }

    public function test_ownership_enforcement_prevents_accessing_another_accounts_character(): void
    {
        $this->actingAs($this->player)
            ->get(route('account.characters.manage', 150001))
            ->assertOk()
            ->assertSee('Freynix')
            ->assertSee('Equip &amp; Status', false);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Character not found or does not belong to your account.');

        $this->service->renameCharacter($this->player, 150003, 'StolenName', 'idem-own-1');
    }

    public function test_player_rename_deducts_cash_points_prevents_duplicates_and_is_idempotent(): void
    {
        // Duplicate name check
        try {
            $this->service->renameCharacter($this->player, 150001, 'OtherHero', 'idem-dup-1');
            $this->fail('Expected duplicate character name exception.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('already taken', $e->getMessage());
        }

        // Successful rename
        $tx1 = $this->service->renameCharacter($this->player, 150001, 'FreynixNew', 'idem-ren-1');
        // Idempotent replay
        $tx2 = $this->service->renameCharacter($this->player, 150001, 'FreynixNew', 'idem-ren-1');

        $this->assertSame($tx1->id, $tx2->id);
        $this->assertSame('FreynixNew', DB::connection('ragnarok')->table('char')->where('char_id', 150001)->value('name'));
        $this->assertSame(4500, (int) DB::connection('ragnarok')->table('acc_reg_num')->where('account_id', 2000001)->where('key', '#CASHPOINTS')->value('value'));
        $this->assertDatabaseHas('character_service_logs', [
            'char_id' => 150001,
            'action_type' => 'service.rename',
            'result' => 'success',
        ]);
    }

    public function test_appearance_stat_reset_and_skill_reset_work_correctly(): void
    {
        $this->service->changeAppearance($this->player, 150001, [
            'hair' => 12,
            'hair_color' => 4,
            'clothes_color' => 2,
        ], 'idem-app-1');

        $charAfterApp = DB::connection('ragnarok')->table('char')->where('char_id', 150001)->first();
        $this->assertSame(12, (int) $charAfterApp->hair);
        $this->assertSame(4, (int) $charAfterApp->hair_color);
        $this->assertSame(2, (int) $charAfterApp->clothes_color);

        $this->service->resetStats($this->player, 150001, 'idem-stat-1');
        $charAfterStats = DB::connection('ragnarok')->table('char')->where('char_id', 150001)->first();
        $this->assertSame(1, (int) $charAfterStats->str);
        $this->assertSame(1, (int) $charAfterStats->agi);
        $this->assertGreaterThan(5, (int) $charAfterStats->status_point);

        $this->service->resetSkills($this->player, 150001, 'idem-skill-1');
        $charAfterSkills = DB::connection('ragnarok')->table('char')->where('char_id', 150001)->first();
        $this->assertSame(19, (int) $charAfterSkills->skill_point);
        $this->assertSame(0, DB::connection('ragnarok')->table('skill')->where('char_id', 150001)->count());

        $this->service->changeJob($this->player, 150001, 4008, 'idem-job-1');
        $this->assertSame(4008, (int) DB::connection('ragnarok')->table('char')->where('char_id', 150001)->value('class'));
    }

    public function test_account_level_gender_change_requires_all_characters_offline_and_updates_account(): void
    {
        // Set second character on same account online
        DB::connection('ragnarok')->table('char')->where('char_id', 150002)->update(['online' => 1]);

        try {
            $this->service->changeAccountGender($this->player, 150001, 'F', 'idem-gen-fail');
            $this->fail('Expected online character block.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('All characters on your game account must be offline', $e->getMessage());
        }

        DB::connection('ragnarok')->table('char')->where('char_id', 150002)->update(['online' => 0]);

        $this->service->changeAccountGender($this->player, 150001, 'F', 'idem-gen-ok');
        $this->assertSame('F', DB::connection('ragnarok')->table('login')->where('account_id', 2000001)->value('sex'));
        $this->assertSame('F', DB::connection('ragnarok')->table('char')->where('char_id', 150002)->value('sex'));
    }

    public function test_lock_deletion_waiting_period_cancellation_and_admin_refund(): void
    {
        $this->service->togglePlayerLock($this->player, 150001, true, 'Protect gear');
        $this->assertTrue(CharacterLock::where('char_id', 150001)->where('is_locked', true)->exists());

        try {
            $this->service->requestDeletion($this->player, 150001);
            $this->fail('Locked character should not be deletable.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Locked characters cannot be deleted', $e->getMessage());
        }

        $this->service->togglePlayerLock($this->player, 150001, false);
        $req = $this->service->requestDeletion($this->player, 150001);
        $this->assertSame('pending', $req->status);
        $this->assertGreaterThan(0, (int) DB::connection('ragnarok')->table('char')->where('char_id', 150001)->value('delete_date'));

        $this->service->cancelDeletion($this->player, 150001);
        $this->assertSame('cancelled', $req->fresh()->status);
        $this->assertSame(0, (int) DB::connection('ragnarok')->table('char')->where('char_id', 150001)->value('delete_date'));

        // Admin give item to Escrow Inbox & refund a paid service transaction
        $tx = $this->service->renameCharacter($this->player, 150001, 'FreynixTwo', 'idem-ren-refund');
        $this->service->adminRefundTransaction($this->admin, $tx->id, 'Player typo');
        $this->assertSame('refunded', $tx->fresh()->status);
        $this->assertSame(5000, (int) DB::connection('ragnarok')->table('acc_reg_num')->where('account_id', 2000001)->where('key', '#CASHPOINTS')->value('value'));

        $this->service->adminGiveItem($this->admin, 150001, 501, 10, 0, false, 'escrow');
        $this->assertDatabaseHas('player_inbox_items', [
            'account_id' => 2000001,
            'item_id' => 501,
            'quantity' => 10,
        ]);
    }

    public function test_admin_permissions_and_online_edit_guard(): void
    {
        $themeManager = app(\App\Services\Theme\ThemeManager::class);
        $installed = array_keys($themeManager->getInstalledThemes());
        $this->assertEqualsCanonicalizing(['aether-dark', 'aether-chronicles', 'aether-prestige', 'elysian'], $installed);
        $this->assertTrue($themeManager->themeExists('elysian'));
        $this->assertFalse($themeManager->themeExists('ragnarok-classic'));

        $this->assertTrue($this->admin->hasCharMgmtPermission('edit_characters'));
        $this->assertFalse($this->player->hasCharMgmtPermission('edit_characters'));

        DB::connection('ragnarok')->table('char')->where('char_id', 150001)->update(['online' => 1]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('currently online');
        $this->service->adminUpdateCharacter($this->admin, 150001, ['zeny' => 999999]);
    }
}
