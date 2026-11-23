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
    }

    public function test_admin_can_view_themes_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/themes');
        $response->assertStatus(200);
        $response->assertSee('Theme Manager');
        $response->assertSee('Aether Dark');
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
}

