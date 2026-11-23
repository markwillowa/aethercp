<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Theme\ThemeManager;
use App\Services\Emulator\JobHelper;
use App\Services\Emulator\GuildEmblemRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdvancedModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guilds_index_page_loads(): void
    {
        $response = $this->get('/guilds');
        $response->assertStatus(200);
        $response->assertSee('Guild Directory');
    }

    public function test_guild_emblem_endpoint_returns_png(): void
    {
        $response = $this->get('/guilds/9999/emblem');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_whos_online_page_loads(): void
    {
        $response = $this->get('/online');
        $response->assertStatus(200);
        $response->assertSee("Total Online");
    }

    public function test_war_of_emperium_page_loads(): void
    {
        $response = $this->get('/woe');
        $response->assertStatus(200);
        $response->assertSee('War of Emperium');
    }

    public function test_job_helper_maps_correct_names_and_badges(): void
    {
        $this->assertSame('Novice', JobHelper::getName(0));
        $this->assertSame('Lord Knight', JobHelper::getName(4008));
        $this->assertSame('Dragon Knight', JobHelper::getName(4211));

        $badge = JobHelper::getBadgeColor(4211);
        $this->assertStringContainsString('purple', $badge);
    }

    public function test_theme_manager_discovers_installed_elysian_theme(): void
    {
        $themeManager = new ThemeManager();
        $installed = $themeManager->getInstalledThemes();

        $this->assertArrayHasKey('aether-dark', $installed);
        $this->assertArrayHasKey('elysian', $installed);
        $this->assertSame('Elysian Mythic', $installed['elysian']['name']);
    }

    public function test_placeholder_emblem_generation(): void
    {
        $png = GuildEmblemRenderer::renderPlaceholder();
        $this->assertNotEmpty($png);
        // PNG magic bytes: \x89PNG
        $this->assertTrue(str_starts_with($png, "\x89PNG"));
    }
}

