<?php

namespace Tests\Feature\Auth;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for authentication routes (/admin/login, /admin/logout, /admin/register).
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Pre-set settings.registration_enabled before providers boot so that
     * Auth::routes(['register' => ...]) in web.php sees the correct value.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../../../bootstrap/app.php';

        $app->beforeBootstrapping(BootProviders::class, function () {
            config(['settings.registration_enabled' => '1']);
        });

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    // ── Login form ────────────────────────────────────────────────────────────

    public function test_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }

    // ── Register form ─────────────────────────────────────────────────────────

    public function test_register_page_loads_when_registration_enabled(): void
    {
        $response = $this->get('/admin/register');
        $response->assertStatus(200);
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/admin/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/admin/register', [
            'name' => 'Another User',
            'email' => $user->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_register_fails_when_passwords_dont_match(): void
    {
        $response = $this->post('/admin/register', [
            'name' => 'New User',
            'email' => 'newuser2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ── Redirects ─────────────────────────────────────────────────────────────

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/login');

        $response->assertRedirect();
    }

    public function test_unauthenticated_user_is_redirected_from_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }
}
