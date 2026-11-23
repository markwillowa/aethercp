<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InstallTest extends TestCase
{
    protected string $lockFile;
    protected bool $wasInitiallyInstalled = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lockFile = storage_path('installed');
        $this->wasInitiallyInstalled = File::exists($this->lockFile);
    }

    protected function tearDown(): void
    {
        if ($this->wasInitiallyInstalled) {
            File::put($this->lockFile, json_encode(['installed' => true]));
        } else {
            if (File::exists($this->lockFile)) {
                File::delete($this->lockFile);
            }
        }
        parent::tearDown();
    }

    public function test_installer_redirects_when_already_installed(): void
    {
        File::put($this->lockFile, json_encode(['installed' => true]));

        $response = $this->get('/install');
        $response->assertRedirect(route('home'));
    }

    public function test_installer_renders_wizard_when_not_installed(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->get('/install');
        $response->assertStatus(200);
        $response->assertSee('AetherCP');
        $response->assertSee('Installation');
        $response->assertSee('System Pre-flight Checks');
    }

    public function test_requirements_endpoint_returns_json(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->getJson('/install/requirements');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'php' => ['version', 'required', 'pass'],
            'extensions',
            'paths',
            'all_pass',
        ]);
    }

    public function test_database_test_endpoint_validates_input(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/test-database', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['db_host', 'db_port', 'db_database', 'db_username']);
    }

    public function test_game_database_test_endpoint_validates_input(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/test-game-database', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['ro_db_host', 'ro_db_port', 'ro_db_database', 'ro_db_username', 'ro_emulator']);
    }

    public function test_setup_environment_endpoint_validates_input_strictly(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/setup-environment', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'app_name', 'app_url', 'server_name',
            'db_host', 'db_port', 'db_database', 'db_username',
            'ro_db_host', 'ro_db_port', 'ro_db_database', 'ro_db_username',
            'ro_emulator', 'ro_renewal', 'ro_password_hash'
        ]);
    }

    public function test_setup_admin_endpoint_validates_input_strictly(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/setup-admin', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['admin_type', 'username', 'email']);
    }

    public function test_setup_admin_fails_when_creating_admin_without_password(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/setup-admin', [
            'admin_type' => 'create_new_account',
            'username' => 'newadmin',
            'email' => 'admin@test.com',
            'password' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_setup_admin_fails_when_game_account_not_found(): void
    {
        if (File::exists($this->lockFile)) {
            File::delete($this->lockFile);
        }

        $response = $this->postJson('/install/setup-admin', [
            'admin_type' => 'designate_game_account',
            'username' => 'nonexistent_character_user_xyz',
            'email' => 'admin@test.com',
        ]);
        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}
