<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_customer_dashboard(): void
    {
        $response = $this->get(route('customer.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_access_customer_dashboard_and_see_own_name(): void
    {
        $customer = User::factory()->create([
            'name' => 'Fulan bin Fulan',
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Fulan bin Fulan');
        $response->assertSee('PANEL CUSTOMER');
        $response->assertSee('Total Pesanan');
        $response->assertSee('Pesanan Selesai');
    }

    public function test_dashboard_redirects_admin_to_admin_panel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_customer_to_customer_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard'));

        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_customer_dashboard_displays_accurate_kpi_counts(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::MenungguKonfirmasi,
        ]);

        Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Diproses,
        ]);

        Order::factory()->create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Selesai,
        ]);

        // Pesanan milik orang lain tidak boleh terhitung
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        Order::factory()->create([
            'user_id' => $otherCustomer->id,
            'status' => OrderStatus::Selesai,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalOrders', 3);
        $response->assertViewHas('pendingOrders', 1);
        $response->assertViewHas('processingOrders', 1);
        $response->assertViewHas('completedOrders', 1);
    }

    public function test_customer_can_access_orders_list(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-TEST-001',
            'status' => OrderStatus::MenungguKonfirmasi,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.orders'));

        $response->assertStatus(200);
        $response->assertSee('RHS-TEST-001');
    }

    public function test_customer_can_filter_orders_by_status(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $pendingOrder = Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-PENDING-001',
            'status' => OrderStatus::MenungguKonfirmasi,
        ]);

        $completedOrder = Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-COMPLETED-001',
            'status' => OrderStatus::Selesai,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.orders', ['status' => 'menunggu_konfirmasi']));

        $response->assertStatus(200);
        $response->assertSee('RHS-PENDING-001');
        $response->assertDontSee('RHS-COMPLETED-001');
    }

    public function test_customer_can_search_orders_by_number(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-APPLE-123',
        ]);

        Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-BANANA-456',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.orders', ['q' => 'APPLE']));

        $response->assertStatus(200);
        $response->assertSee('RHS-APPLE-123');
        $response->assertDontSee('RHS-BANANA-456');
    }

    public function test_customer_can_view_own_order_detail(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-DETAIL-001',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.orders.show', $order->order_number));

        $response->assertStatus(200);
        $response->assertSee('RHS-DETAIL-001');
        $response->assertSee('Rincian Produk');
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);

        $orderB = Order::factory()->create([
            'user_id' => $customerB->id,
            'order_number' => 'RHS-PRIVATE-B',
        ]);

        $response = $this->actingAs($customerA)->get(route('customer.orders.show', $orderB->order_number));

        $response->assertStatus(403);
    }
}

