<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Character\CharacterCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OnlineCharacterCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up an in-memory SQLite connection for the ragnarok DB during tests
        Config::set('database.connections.ragnarok', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        DB::purge('ragnarok');

        $roSchema = Schema::connection('ragnarok');

        $roSchema->create('login', function ($table) {
            $table->increments('account_id');
            $table->string('userid', 23);
            $table->string('user_pass', 64)->default('');
            $table->string('sex', 1)->default('M');
            $table->string('email', 100)->default('');
            $table->integer('group_id')->default(0);
            $table->integer('state')->default(0);
            $table->integer('character_slots')->default(9);
        });

        $roSchema->create('char', function ($table) {
            $table->increments('char_id');
            $table->integer('account_id');
            $table->integer('char_num')->default(0);
            $table->string('name', 30);
            $table->integer('class')->default(0);
            $table->integer('base_level')->default(1);
            $table->integer('job_level')->default(1);
            $table->integer('zeny')->default(0);
            $table->integer('str')->default(1);
            $table->integer('agi')->default(1);
            $table->integer('vit')->default(1);
            $table->integer('int')->default(1);
            $table->integer('dex')->default(1);
            $table->integer('luk')->default(1);
            $table->integer('max_hp')->default(40);
            $table->integer('hp')->default(40);
            $table->integer('max_sp')->default(11);
            $table->integer('sp')->default(11);
            $table->integer('status_point')->default(48);
            $table->integer('hair')->default(1);
            $table->integer('hair_color')->default(0);
            $table->string('last_map', 32)->default('prontera');
            $table->integer('last_x')->default(156);
            $table->integer('last_y')->default(191);
            $table->string('save_map', 32)->default('prontera');
            $table->integer('save_x')->default(156);
            $table->integer('save_y')->default(191);
            $table->integer('online')->default(0);
            $table->string('sex', 1)->default('M');
        });

        $roSchema->create('inventory', function ($table) {
            $table->increments('id');
            $table->integer('char_id');
            $table->integer('nameid');
            $table->integer('amount')->default(1);
            $table->integer('equip')->default(0);
            $table->integer('identify')->default(1);
        });
    }

    public function test_admin_can_configure_presets_and_player_sees_only_enabled_jobs(): void
    {
        $admin = User::factory()->create(['username' => 'roadmin', 'role' => 'admin']);
        $player = User::factory()->create(['username' => 'roplayer', 'account_id' => 2000001]);

        DB::connection('ragnarok')->table('login')->insert([
            'account_id' => 2000001,
            'userid' => 'roplayer',
            'sex' => 'M',
            'character_slots' => 9,
        ]);

        // Apply Transcendent preset via admin
        $this->actingAs($admin)
            ->post(route('admin.character-creation.preset'), ['preset' => 'transcendent'])
            ->assertRedirect();

        $response = $this->actingAs($player)->get(route('account.characters.create'));
        $response->assertStatus(200);
        $response->assertSee('High Novice');
        $response->assertSee('High Swordsman');
        $response->assertDontSee('Rune Knight');
    }

    public function test_player_cannot_submit_unauthorized_job_and_can_create_valid_character(): void
    {
        $player = User::factory()->create(['username' => 'hero1', 'account_id' => 2000002]);

        DB::connection('ragnarok')->table('login')->insert([
            'account_id' => 2000002,
            'userid' => 'hero1',
            'sex' => 'M',
            'character_slots' => 9,
        ]);

        /** @var CharacterCreationService $service */
        $service = app(CharacterCreationService::class);
        $service->saveSettings([
            'enabled' => '1',
            'enabled_categories' => ['novice'],
            'enabled_jobs' => [0],
            'starting_base_level' => '1',
            'starting_job_level' => '1',
            'allow_custom_levels' => '0',
            'starting_items' => [
                'global' => [['item_id' => 501, 'amount' => 5]],
                'jobs' => ['0' => [['item_id' => 1201, 'amount' => 1]]],
            ],
        ]);

        // Attempt unauthorized job ID (e.g. Rune Knight 4054)
        $this->actingAs($player)
            ->post(route('account.characters.store'), [
                'name' => 'SneakyKnight',
                'gender' => 'F',
                'hair_style' => 1,
                'hair_color' => 0,
                'job_id' => 4054,
                'base_level' => 99,
                'job_level' => 50,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('char', 0, 'ragnarok');

        // Valid Novice creation (even if browser tries to submit base_level 99, server enforces 1)
        $this->actingAs($player)
            ->post(route('account.characters.store'), [
                'name' => 'AetherNovice',
                'gender' => 'F',
                'hair_style' => 3,
                'hair_color' => 2,
                'job_id' => 0,
                'base_level' => 99,
                'job_level' => 50,
            ])
            ->assertRedirect(route('account.characters'))
            ->assertSessionHas('success');

        $char = DB::connection('ragnarok')->table('char')->where('name', 'AetherNovice')->first();
        $this->assertNotNull($char);
        $this->assertSame(0, (int) $char->class);
        $this->assertSame(1, (int) $char->base_level);
        $this->assertSame(1, (int) $char->job_level);
        $this->assertSame(3, (int) $char->hair);
        $this->assertSame(2, (int) $char->hair_color);
        $this->assertSame('F', $char->sex);

        // Account-level gender updated since it was the first character
        $login = DB::connection('ragnarok')->table('login')->where('account_id', 2000002)->first();
        $this->assertSame('F', $login->sex);

        // Starting items inserted into inventory
        $invCount = DB::connection('ragnarok')->table('inventory')->where('char_id', $char->char_id)->count();
        $this->assertSame(2, $invCount);
    }
}
