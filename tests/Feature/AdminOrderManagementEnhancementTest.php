<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderManagementEnhancementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_orders_index_gracefully_handles_both_member_and_guest_orders(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $member = User::factory()->create([
            'name' => 'Member Setia',
            'email' => 'membersetia@example.com',
            'role' => 'customer',
        ]);

        // Pesanan member
        $memberOrder = Order::factory()->create([
            'user_id' => $member->id,
            'order_number' => 'RHS-MEMBER-001',
            'customer_name' => 'Member Setia',
        ]);

        // Pesanan tamu / lama tanpa user_id
        $guestOrder = Order::factory()->create([
            'user_id' => null,
            'order_number' => 'RHS-GUEST-001',
            'customer_name' => 'Pelanggan Tamu',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('RHS-MEMBER-001');
        $response->assertSee('Member');
        $response->assertSee('RHS-GUEST-001');
        $response->assertSee('Tamu / Pesanan Lama');
    }

    public function test_admin_order_show_displays_member_info_when_user_exists(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $member = User::factory()->create([
            'name' => 'Member Terdaftar',
            'email' => 'terdaftar@example.com',
            'role' => 'customer',
        ]);

        $order = Order::factory()->create([
            'user_id' => $member->id,
            'order_number' => 'RHS-ORDER-INFO',
            'customer_name' => 'Member Terdaftar',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('Member Terdaftar');
        $response->assertSee('terdaftar@example.com');
    }

    public function test_admin_order_show_displays_guest_fallback_when_user_is_null(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $order = Order::factory()->create([
            'user_id' => null,
            'order_number' => 'RHS-LEGACY-001',
            'customer_name' => 'Tamu Offline',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('Tamu / Pelanggan Lama');
    }
}
