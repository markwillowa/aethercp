<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\News;
use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic news and download
        News::create([
            'title' => 'Test News',
            'slug' => 'test-news',
            'category' => 'news',
            'summary' => 'Test summary',
            'content' => 'Test content',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Download::create([
            'title' => 'Test Client',
            'category' => 'Full Client',
            'version' => '1.0',
            'file_size' => '1 GB',
            'url_mirror1' => 'https://example.com/client',
            'is_active' => true,
        ]);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(config('aether.name'));
    }

    public function test_news_index_and_detail_load(): void
    {
        $resIndex = $this->get('/news');
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Test News');

        $resShow = $this->get('/news/test-news');
        $resShow->assertStatus(200);
        $resShow->assertSee('Test content');
    }

    public function test_downloads_page_loads(): void
    {
        $response = $this->get('/downloads');
        $response->assertStatus(200);
        $response->assertSee('Test Client');
    }

    public function test_donations_page_loads(): void
    {
        $response = $this->get('/donations');
        $response->assertStatus(200);
    }

    public function test_item_database_search_works_with_text_and_numeric_query(): void
    {
        $resText = $this->get('/database/items?q=card');
        $resText->assertStatus(200);
        $resText->assertSee('Item Database Search');

        $resNum = $this->get('/database/items?q=501');
        $resNum->assertStatus(200);
        $resNum->assertSee('Item Database Search');
    }

    public function test_login_and_register_pages_load(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_admin_dashboard_protected_from_guests(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_forbidden_for_regular_user(): void
    {
        $user = User::create([
            'username' => 'regularplayer',
            'name' => 'Regular Player',
            'email' => 'player@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_dashboard_accessible_by_admin(): void
    {
        $admin = User::create([
            'username' => 'gamemaster',
            'name' => 'Game Master',
            'email' => 'gm@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Administration Dashboard');
    }
}

