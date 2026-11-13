<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan route GET /forgot-password telah dinonaktifkan (404 Not Found).
     */
    public function test_forgot_password_screen_cannot_be_rendered_returns_404(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(404);
    }

    /**
     * Memastikan endpoint POST /forgot-password telah dinonaktifkan (404 Not Found).
     */
    public function test_forgot_password_endpoint_cannot_be_requested_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(404);
    }

    /**
     * Memastikan route GET /reset-password/{token} telah dinonaktifkan (404 Not Found).
     */
    public function test_reset_password_screen_cannot_be_rendered_returns_404(): void
    {
        $response = $this->get('/reset-password/sample-token-12345');

        $response->assertStatus(404);
    }

    /**
     * Memastikan endpoint POST /reset-password telah dinonaktifkan (404 Not Found).
     */
    public function test_password_cannot_be_reset_via_email_token_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token' => 'sample-token-12345',
            'email' => $user->email,
            'password' => 'NewValidPassword123!',
            'password_confirmation' => 'NewValidPassword123!',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Memastikan halaman login tidak lagi memuat tautan Lupa kata sandi.
     */
    public function test_login_page_does_not_contain_forgot_password_link(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('Lupa kata sandi?');
        $response->assertDontSee('/forgot-password');
    }
}
