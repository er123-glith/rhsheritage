<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\AdminActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCrudAndDummyRefactoringTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $otherAdmin;
    private User $customer;
    private User $customer2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'     => 'Owner Administrator',
            'email'    => 'owner@rhsheritage.com',
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $this->otherAdmin = User::factory()->create([
            'name'     => 'Staff Admin',
            'email'    => 'staff@rhsheritage.com',
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $this->customer = User::factory()->create([
            'name'     => 'Customer Utama',
            'email'    => 'customer.utama@example.com',
            'phone'    => '081234567890',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $this->customer2 = User::factory()->create([
            'name'     => 'Customer Kedua',
            'email'    => 'customer.kedua@example.com',
            'phone'    => '081298765432',
            'role'     => 'customer',
            'is_owner' => false,
        ]);
    }

    /**
     * 1-7: REFACTORING DATA DUMMY TESTING
     */
    public function test_admin_dashboard_metrics_use_actual_database_values(): void
    {
        $category = Category::create(['name' => 'Kategori Tes', 'slug' => 'kategori-tes', 'sort_order' => 1]);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Produk Tes',
            'slug'        => 'produk-tes',
            'base_price'  => 50000,
            'weight_gram' => 200,
            'is_active'   => true,
        ]);
        $variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'TES-SKU-01',
            'variant_label'  => 'Ukuran Standar',
            'attribute_map'  => ['Ukuran' => 'Standar'],
            'price'          => 50000,
            'stock_quantity' => 15,
            'is_active'      => true,
        ]);

        // Buat 1 pesanan selesai dan 1 pesanan menunggu
        $zone = ShippingZone::create(['name' => 'Jawa Timur', 'base_rate' => 10000, 'sort_order' => 1, 'is_active' => true]);

        Order::create([
            'user_id'            => $this->customer->id,
            'order_number'       => 'ORD-REAL-001',
            'customer_name'      => $this->customer->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Asli No. 1',
            'city'               => 'Surabaya',
            'province'           => 'Jawa Timur',
            'postal_code'        => '60111',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 50000,
            'shipping_cost'      => 10000,
            'total'              => 60000,
            'status'             => OrderStatus::Selesai,
        ]);

        Order::create([
            'user_id'            => $this->customer->id,
            'order_number'       => 'ORD-REAL-002',
            'customer_name'      => $this->customer->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Asli No. 1',
            'city'               => 'Surabaya',
            'province'           => 'Jawa Timur',
            'postal_code'        => '60111',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 50000,
            'shipping_cost'      => 10000,
            'total'              => 60000,
            'status'             => OrderStatus::MenungguKonfirmasi,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Assert data passing to view
        $response->assertViewHas('totalOrdersCount', 2);
        $response->assertViewHas('pendingOrdersCount', 1);
        $response->assertViewHas('completedOrdersCount', 1);
        $response->assertViewHas('totalRevenue', 60000.0);
        $response->assertViewHas('totalCustomersCount', 2); // customer + customer2
        $response->assertViewHas('activeProductsCount', 1);
        $response->assertViewHas('totalStockCount', 15);
    }

    public function test_customer_dashboard_only_calculates_own_orders_and_spending(): void
    {
        $zone = ShippingZone::create(['name' => 'Jawa', 'base_rate' => 10000, 'sort_order' => 1, 'is_active' => true]);

        // Pesanan milik Customer Utama (100.000 selesai)
        Order::create([
            'user_id'            => $this->customer->id,
            'order_number'       => 'ORD-CUST-1',
            'customer_name'      => $this->customer->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Alamat',
            'city'               => 'Kota',
            'province'           => 'Provinsi',
            'postal_code'        => '12345',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram'  => 100,
            'subtotal'           => 90000,
            'shipping_cost'      => 10000,
            'total'              => 100000,
            'status'             => OrderStatus::Selesai,
        ]);

        // Pesanan milik Customer Kedua (500.000 selesai) — TIDAK boleh terhitung di customer utama
        Order::create([
            'user_id'            => $this->customer2->id,
            'order_number'       => 'ORD-CUST-2',
            'customer_name'      => $this->customer2->name,
            'customer_phone'     => '081298765432',
            'address_line'       => 'Alamat 2',
            'city'               => 'Kota 2',
            'province'           => 'Provinsi 2',
            'postal_code'        => '12345',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram'  => 100,
            'subtotal'           => 490000,
            'shipping_cost'      => 10000,
            'total'              => 500000,
            'status'             => OrderStatus::Selesai,
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);

        $response->assertViewHas('totalOrders', 1);
        $response->assertViewHas('completedOrders', 1);
        $response->assertViewHas('totalSpent', 100000.0);
        $response->assertSee('Rp100.000');
        $response->assertDontSee('Rp500.000');
    }

    /**
     * 8-18: EDIT CUSTOMER TESTING
     */
    public function test_admin_can_view_edit_customer_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.edit', $this->customer));

        $response->assertStatus(200);
        $response->assertSee('Edit Informasi Pelanggan');
        $response->assertSee($this->customer->name);
        $response->assertSee($this->customer->email);
    }

    public function test_admin_can_update_customer_name_email_and_phone(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->customer), [
            'name'  => 'Budi Santoso Diperbarui',
            'email' => 'budi.baru@example.com',
            'phone' => '081999888777',
        ]);

        $response->assertRedirect(route('admin.customers.show', $this->customer));
        $response->assertSessionHas('status');

        $this->customer->refresh();
        $this->assertEquals('Budi Santoso Diperbarui', $this->customer->name);
        $this->assertEquals('budi.baru@example.com', $this->customer->email);
        $this->assertEquals('081999888777', $this->customer->phone);

        // Verifikasi log aktivitas tercatat
        $log = AdminActivityLog::where('target_user_id', $this->customer->id)->where('action', 'update')->first();
        $this->assertNotNull($log);
        $this->assertEquals($this->admin->id, $log->admin_id);
    }

    public function test_customer_email_must_be_unique_excluding_own_email(): void
    {
        // Mencoba menggunakan email milik customer2
        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->customer), [
            'name'  => 'Nama Baru',
            'email' => $this->customer2->email, // duplikat
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_customer_edit_rejects_invalid_email_and_too_long_input(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->customer), [
            'name'  => str_repeat('A', 300), // max 255
            'email' => 'bukan-email-valid',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_customer_edit_ignores_role_is_owner_or_password_injection(): void
    {
        $oldPassword = $this->customer->password;

        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->customer), [
            'name'     => 'Customer Kebal Modifikasi',
            'email'    => 'kebal@example.com',
            'role'     => 'admin', // percobaan eskalasi role
            'is_owner' => 1,       // percobaan eskalasi owner
            'password' => 'HackedPassword123!', // percobaan ubah password
        ]);

        $this->customer->refresh();
        $this->assertEquals('customer', $this->customer->role);
        $this->assertFalse((bool) $this->customer->is_owner);
        $this->assertEquals($oldPassword, $this->customer->password);
    }

    public function test_customer_cannot_access_admin_edit_customer(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.customers.edit', $this->customer2));
        $response->assertStatus(403);

        $responseUpdate = $this->actingAs($this->customer)->put(route('admin.customers.update', $this->customer2), [
            'name'  => 'Hack Name',
            'email' => 'hack@example.com',
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_admin_cannot_edit_admin_account_via_customer_endpoint(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.edit', $this->otherAdmin));
        $response->assertStatus(404);

        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->otherAdmin), [
            'name'  => 'Ubah Admin',
            'email' => 'ubahadmin@example.com',
        ]);
        $responseUpdate->assertStatus(403);
    }

    /**
     * 19-30: SOFT DELETE CUSTOMER TESTING
     */
    public function test_admin_can_soft_delete_customer_with_confirmation_word(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $this->customer), [
            'confirmation' => 'HAPUS',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('status');

        $this->customer->refresh();
        $this->assertTrue($this->customer->trashed());
        $this->assertNotNull($this->customer->deleted_at);

        // Verifikasi log tercatat
        $log = AdminActivityLog::where('target_user_id', $this->customer->id)->where('action', 'delete')->first();
        $this->assertNotNull($log);
        $this->assertEquals('delete', $log->action);
    }

    public function test_soft_delete_requires_exact_hapus_confirmation(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $this->customer), [
            'confirmation' => 'hapus-kecil-salah',
        ]);

        $response->assertSessionHasErrors('confirmation');
        $this->assertFalse($this->customer->fresh()->trashed());
    }

    public function test_admin_cannot_delete_self_or_other_admin_via_customer_endpoint(): void
    {
        // Admin hapus diri sendiri
        $responseSelf = $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $this->admin), [
            'confirmation' => 'HAPUS',
        ]);
        $responseSelf->assertSessionHas('error');
        $this->assertFalse($this->admin->fresh()->trashed());

        // Admin hapus admin lain via endpoint customer
        $responseOther = $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $this->otherAdmin), [
            'confirmation' => 'HAPUS',
        ]);
        $responseOther->assertStatus(403);
        $this->assertFalse($this->otherAdmin->fresh()->trashed());
    }

    public function test_guest_or_customer_cannot_delete_customer(): void
    {
        $responseGuest = $this->delete(route('admin.customers.destroy', $this->customer), [
            'confirmation' => 'HAPUS',
        ]);
        $responseGuest->assertRedirect(route('admin.login'));

        $responseCustomer = $this->actingAs($this->customer)->delete(route('admin.customers.destroy', $this->customer2), [
            'confirmation' => 'HAPUS',
        ]);
        $responseCustomer->assertStatus(403);
    }

    public function test_soft_deleted_customer_cannot_login(): void
    {
        // Nonaktifkan customer
        $this->customer->delete();

        $response = $this->post('/login', [
            'email'    => $this->customer->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_soft_deleted_customer_active_session_is_terminated_and_blocked(): void
    {
        // Asumsikan customer login
        $this->actingAs($this->customer);

        // Admin menonaktifkan customer di backend
        $this->customer->delete();

        // Customer yang sudah di-soft-delete mencoba membuka dashboard
        $response = $this->get(route('customer.dashboard'));

        // Middleware EnsureAccountIsActive memutus sesi dan mengarahkan ke login
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_historical_orders_and_items_remain_intact_after_customer_soft_delete(): void
    {
        $zone = ShippingZone::create(['name' => 'Zona A', 'base_rate' => 10000, 'sort_order' => 1, 'is_active' => true]);
        $order = Order::create([
            'user_id'            => $this->customer->id,
            'order_number'       => 'HIST-001',
            'customer_name'      => $this->customer->name,
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Sejarah',
            'city'               => 'Bandung',
            'province'           => 'Jawa Barat',
            'postal_code'        => '40111',
            'shipping_zone_id'   => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram'  => 100,
            'subtotal'           => 100000,
            'shipping_cost'      => 10000,
            'total'              => 110000,
            'status'             => OrderStatus::Selesai,
        ]);

        $orderItem = OrderItem::create([
            'order_id'      => $order->id,
            'product_name'  => 'Songkok Bersejarah',
            'variant_label' => 'No. 9',
            'unit_price'    => 100000,
            'quantity'      => 1,
            'subtotal'      => 100000,
        ]);

        // Soft delete akun customer
        $this->customer->delete();

        // Verifikasi Order & OrderItem tetap ada dan terhubung
        $freshOrder = Order::where('order_number', 'HIST-001')->first();
        $this->assertNotNull($freshOrder);
        $this->assertEquals($this->customer->id, $freshOrder->user_id);
        $this->assertEquals('Songkok Bersejarah', $freshOrder->items()->first()->product_name);
        // Relasi user withTrashed tetap dapat mengambil nama customer
        $this->assertEquals($this->customer->name, $freshOrder->user->name);
    }
}
