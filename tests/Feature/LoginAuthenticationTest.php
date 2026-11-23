<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Contracts\Repositories\AccountRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginAuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_with_seeded_admin_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'Admin1234!',
        ]);

        $response->assertRedirect('/account/profile');
        $this->assertAuthenticated();
    }

    public function test_login_with_server_owner_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'malda009',
            'password' => 'Maki0913$',
        ]);

        $response->assertRedirect('/account/profile');
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'IncorrectPassword999',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
