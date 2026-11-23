<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'username' => 'superadmin',
            'name' => 'Super Administrator',
            'email' => 'super@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_modules_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/modules');
        $response->assertStatus(200);
        $response->assertSee('Modules Manager');
        $response->assertSee('Account System');
        $response->assertSee('Vending & Market Tracker');
    }

    public function test_admin_can_toggle_module_and_routes_are_enforced(): void
    {
        // 1. Module is initially enabled
        $this->get('/donations')->assertStatus(200);

        // 2. Admin toggles donations module OFF
        $toggleRes = $this->actingAs($this->admin)->post('/admin/modules/donations/toggle');
        $toggleRes->assertRedirect();

        // 3. Visiting disabled module returns 404
        $this->get('/donations')->assertStatus(404);

        // 4. Admin toggles donations module back ON
        $this->actingAs($this->admin)->post('/admin/modules/donations/toggle');
        $this->get('/donations')->assertStatus(200);
    }

    public function test_admin_can_view_themes_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/themes');
        $response->assertStatus(200);
        $response->assertSee('Theme Manager');
        $response->assertSee('Aether Dark');
    }

    public function test_admin_can_activate_theme_and_views_switch_successfully(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/themes/elysian/activate');
        $response->assertRedirect();

        $home = $this->get('/');
        $home->assertStatus(200);
        $home->assertSee('VALKYRIE REALM');
        $home->assertSee('Ascend to');

        // Revert back to core theme for clean state
        $this->actingAs($this->admin)->post('/admin/themes/aether-dark/activate');
    }

    public function test_admin_can_publish_news_article(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/news', [
            'title' => 'Emergency Patch 1.0.1',
            'category' => 'maintenance',
            'summary' => 'Short summary for emergency maintenance',
            'content' => 'Server will be restarted in 10 minutes.',
            'is_published' => 1,
            'is_pinned' => 1,
        ]);

        $response->assertRedirect('/admin/news');
        $this->assertDatabaseHas('news', [
            'title' => 'Emergency Patch 1.0.1',
            'category' => 'maintenance',
        ]);
    }

    public function test_admin_can_view_activity_logs(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/logs');
        $response->assertStatus(200);
        $response->assertSee('Activity Logs');
    }

    public function test_admin_can_view_guilds_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/guilds');
        $response->assertStatus(200);
        $response->assertSee('Guild Records');
    }

    public function test_admin_can_manage_downloads(): void
    {
        // 1. View downloads page
        $res = $this->actingAs($this->admin)->get('/admin/downloads');
        $res->assertStatus(200);
        $res->assertSee('Downloads &amp; Client Manager', false);

        // 2. Create download package
        $createRes = $this->actingAs($this->admin)->post('/admin/downloads', [
            'title' => 'FreynixRO Full Client v2.0',
            'category' => 'Full Client',
            'version' => '2.0.0',
            'file_size' => '2.8 GB',
            'url_mirror1' => 'https://downloads.example.com/client.zip',
            'url_mirror2' => 'https://mirror2.example.com/client.zip',
            'description' => 'Official full installer with updated Gepard Shield.',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $createRes->assertRedirect();

        $this->assertDatabaseHas('downloads', [
            'title' => 'FreynixRO Full Client v2.0',
            'category' => 'Full Client',
            'version' => '2.0.0',
            'file_size' => '2.8 GB',
        ]);

        $download = \App\Models\Download::where('title', 'FreynixRO Full Client v2.0')->first();

        // 3. Update download package
        $updateRes = $this->actingAs($this->admin)->put("/admin/downloads/{$download->id}", [
            'title' => 'FreynixRO Full Client v2.1',
            'category' => 'Full Client',
            'version' => '2.1.0',
            'file_size' => '2.9 GB',
            'url_mirror1' => 'https://downloads.example.com/client_v2.1.zip',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $updateRes->assertRedirect();
        $this->assertDatabaseHas('downloads', ['title' => 'FreynixRO Full Client v2.1', 'version' => '2.1.0']);

        // 4. Toggle active status
        $toggleRes = $this->actingAs($this->admin)->post("/admin/downloads/{$download->id}/toggle");
        $toggleRes->assertRedirect();
        $this->assertFalse((bool) $download->fresh()->is_active);

        // 5. Delete download package
        $delRes = $this->actingAs($this->admin)->delete("/admin/downloads/{$download->id}");
        $delRes->assertRedirect();
        $this->assertDatabaseMissing('downloads', ['id' => $download->id]);
    }
}

