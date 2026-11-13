<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_logout_via_post(): void
    {
        $admin = User::factory()->create([
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('status', 'Anda telah berhasil keluar.');
    }

    public function test_admin_can_logout_via_get_without_method_not_allowed_exception(): void
    {
        $admin = User::factory()->create([
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/logout');

        $this->assertGuest();
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('status', 'Anda telah berhasil keluar.');
    }

    public function test_guest_accessing_admin_logout_via_get_redirects_gracefully(): void
    {
        $response = $this->get('/admin/logout');

        $this->assertGuest();
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_customer_accessing_admin_logout_via_get_terminates_session_and_redirects(): void
    {
        $customer = User::factory()->create([
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->get('/admin/logout');

        $this->assertGuest();
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_customer_can_logout_via_post(): void
    {
        $customer = User::factory()->create([
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar.');
    }

    public function test_customer_can_logout_via_get_without_exception(): void
    {
        $customer = User::factory()->create([
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->get('/logout');

        $this->assertGuest();
        $response->assertStatus(302);
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar.');
    }

    public function test_admin_blade_view_contains_post_form_for_logout(): void
    {
        $admin = User::factory()->create([
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $view = $this->actingAs($admin)->blade(
            '<x-layouts.admin title="Test"><p>Content</p></x-layouts.admin>'
        );

        $view->assertSee('action="' . route('admin.logout') . '"', false);
        $view->assertSee('method="POST"', false);
    }

    public function test_customer_blade_view_contains_post_form_for_logout(): void
    {
        $customer = User::factory()->create([
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $view = $this->actingAs($customer)->blade(
            '<x-layouts.customer title="Test"><p>Content</p></x-layouts.customer>'
        );

        $view->assertSee('action="' . route('logout') . '"', false);
        $view->assertSee('method="POST"', false);
    }
}
