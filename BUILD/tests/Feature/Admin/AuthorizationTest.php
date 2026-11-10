<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Panel Masuk Owner');
        $response->assertSee('RHS HERITAGE');
    }

    public function test_owner_can_authenticate_using_valid_credentials(): void
    {
        $owner = User::factory()->create([
            'email' => 'owner@rhsheritage.example',
            'password' => bcrypt('password123'),
            'is_owner' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'owner@rhsheritage.example',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($owner);
        $response->assertRedirect('/admin');
    }

    public function test_non_owner_cannot_authenticate_to_admin(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'is_owner' => false,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_non_owner_cannot_access_dashboard_directly(): void
    {
        $nonOwner = User::factory()->create([
            'email' => 'user2@example.com',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($nonOwner)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_owner_cannot_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'owner@rhsheritage.example',
            'password' => bcrypt('password123'),
            'is_owner' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'owner@rhsheritage.example',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_rate_limiter_throttles_after_five_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'owner@rhsheritage.example',
            'password' => bcrypt('password123'),
            'is_owner' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'owner@rhsheritage.example',
                'password' => 'wrong-password',
            ]);
        }

        // Attempt 6 should be throttled
        $response = $this->post('/admin/login', [
            'email' => 'owner@rhsheritage.example',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk', $errors[0]);
    }

    public function test_owner_can_log_out(): void
    {
        $owner = User::factory()->create([
            'is_owner' => true,
        ]);

        $response = $this->actingAs($owner)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_owner_can_view_dashboard(): void
    {
        $owner = User::factory()->create([
            'name' => 'Owner Test',
            'is_owner' => true,
        ]);

        $response = $this->actingAs($owner)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Dashboard');
        $response->assertSee('Pesanan Baru');
        $response->assertSee('Stok Menipis');
        $response->assertSee('Pengajuan Mitra');
    }
}
