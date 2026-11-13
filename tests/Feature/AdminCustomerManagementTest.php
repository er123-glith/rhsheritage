<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_customers(): void
    {
        $response = $this->get(route('admin.customers.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_customer_cannot_access_admin_customers(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($customer)->get(route('admin.customers.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_customers_list_and_only_see_customers(): void
    {
        $admin = User::factory()->create([
            'name' => 'Owner Admin',
            'email' => 'owner-panel@rhsheritage.com',
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $otherAdmin = User::factory()->create([
            'name' => 'Akun Admin Lain',
            'email' => 'otheradmin@rhsheritage.com',
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $customer1 = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $customer2 = User::factory()->create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Rahma');
        // Admin lain tidak boleh masuk ke dalam tabel data pelanggan
        $response->assertDontSee('Akun Admin Lain');
        $response->assertDontSee('otheradmin@rhsheritage.com');
        $response->assertViewHas('customers', function ($customers) {
            return $customers->where('role', 'admin')->isEmpty() && $customers->count() === 2;
        });
    }

    public function test_admin_can_search_customers(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        User::factory()->create([
            'name' => 'Ahmad Dahlan',
            'email' => 'ahmad@example.com',
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'Zulkifli Lubis',
            'email' => 'zul@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.index', ['q' => 'Ahmad']));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Dahlan');
        $response->assertDontSee('Zulkifli Lubis');
    }

    public function test_admin_can_view_customer_profile_and_order_history(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $customer = User::factory()->create([
            'name' => 'Dewi Sartika',
            'email' => 'dewi@example.com',
            'role' => 'customer',
            'is_owner' => false,
        ]);

        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-DEWI-001',
            'status' => OrderStatus::Selesai,
            'total' => 150000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.show', $customer));

        $response->assertStatus(200);
        $response->assertSee('Dewi Sartika');
        $response->assertSee('dewi@example.com');
        $response->assertSee('RHS-DEWI-001');
        $response->assertSee('150.000');
    }

    public function test_admin_cannot_access_admin_account_via_customer_show_route(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $otherAdmin = User::factory()->create([
            'role' => 'admin',
            'is_owner' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.show', $otherAdmin));

        $response->assertStatus(404);
    }
}
