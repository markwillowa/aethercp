<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Module\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EscrowAndModuleMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_escrow_inbox_renders_cleanly_when_empty_and_on_unclaimed(): void
    {
        $user = User::factory()->create([
            'username' => 'escrowtester',
            'account_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('escrow.index'))
            ->assertStatus(200)
            ->assertSee('Your Escrow Inbox is Empty');

        $this->actingAs($user)
            ->get(route('escrow.index', ['status' => 'unclaimed']))
            ->assertStatus(200)
            ->assertSee('Your Escrow Inbox is Empty');

        $this->actingAs($user)
            ->get(route('escrow.index', ['status' => 'claimed']))
            ->assertStatus(200)
            ->assertSee('No Claimed History Found');
    }

    public function test_enabling_module_auto_migrates_missing_tables(): void
    {
        // Drop forum tables to simulate a module whose SQL/migrations haven't been run yet
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('reputation_audit_logs');
        Schema::dropIfExists('reputation_transactions');
        Schema::dropIfExists('reputation_levels');
        Schema::dropIfExists('forum_user_profiles');
        Schema::dropIfExists('forum_reports');
        Schema::dropIfExists('forum_topic_subscriptions');
        Schema::dropIfExists('forum_post_attachments');
        Schema::dropIfExists('forum_post_reactions');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_boards');
        Schema::dropIfExists('forum_categories');
        Schema::enableForeignKeyConstraints();

        // Also remove the migration records so Artisan::call('migrate') sees them as pending
        \DB::table('migrations')->whereIn('migration', [
            '2026_11_26_000002_create_forum_tables',
            '2026_11_26_000003_create_forum_identity_and_reputation_tables',
        ])->delete();

        $this->assertFalse(Schema::hasTable('forum_categories'));

        $modMgr = app(ModuleManager::class);

        // Disable then re-enable forum module
        $modMgr->toggle('forum');
        $this->assertFalse($modMgr->isEnabled('forum'));

        $newState = $modMgr->toggle('forum');
        $this->assertTrue($newState);
        $this->assertTrue(Schema::hasTable('forum_categories'));
        $this->assertTrue(Schema::hasTable('reputation_levels'));
    }

    public function test_site_setting_and_module_manager_do_not_crash_when_table_is_missing(): void
    {
        Schema::dropIfExists('site_settings');

        $this->assertSame('fallback', \App\Models\SiteSetting::get('nonexistent_key', 'fallback'));
        $modules = app(ModuleManager::class)->getModules();
        $this->assertArrayHasKey('account', $modules);
        $this->assertTrue($modules['account']['enabled']);
    }
}
