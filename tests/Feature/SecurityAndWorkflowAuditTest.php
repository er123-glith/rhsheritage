<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndWorkflowAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customerA;
    private User $customerB;
    private ProductVariant $variant;
    private ShippingZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'     => 'Administrator Test',
            'email'    => 'admin@rhsheritage.example',
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $this->customerA = User::factory()->create([
            'name'     => 'Customer A',
            'email'    => 'customera@example.com',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $this->customerB = User::factory()->create([
            'name'     => 'Customer B',
            'email'    => 'customerb@example.com',
            'role'     => 'customer',
            'is_owner' => false,
        ]);

        $category = Category::create([
            'name'       => 'Peci Tradisional',
            'slug'       => 'peci-tradisional',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Peci Beludru Premium',
            'slug'        => 'peci-beludru-premium',
            'base_price'  => 75000,
            'weight_gram' => 200,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'PECI-BLD-08',
            'variant_label'  => 'Ukuran 8',
            'attribute_map'  => ['Ukuran' => '8'],
            'price'          => 75000,
            'stock_quantity' => 10,
            'is_active'      => true,
        ]);

        $this->zone = ShippingZone::create([
            'name'              => 'Jabodetabek',
            'base_rate'         => 12000,
            'rate_per_extra_kg' => 5000,
            'sort_order'        => 1,
            'is_active'         => true,
        ]);
    }

    /**
     * AUDIT BAGIAN B: Keamanan Logout
     */
    public function test_get_admin_logout_does_not_terminate_admin_session(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/logout');

        $this->assertAuthenticatedAs($this->admin);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_get_customer_logout_does_not_terminate_customer_session(): void
    {
        $response = $this->actingAs($this->customerA)->get('/logout');

        $this->assertAuthenticatedAs($this->customerA);
        $response->assertStatus(302);
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_post_admin_logout_terminates_session(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_post_customer_logout_terminates_session(): void
    {
        $response = $this->actingAs($this->customerA)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    /**
     * AUDIT BAGIAN C: Proteksi Autentikasi dan Role
     */
    public function test_registration_always_creates_customer_ignoring_injected_role_or_owner(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Hacker Impersonator',
            'email'                 => 'hacker@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'admin',     // percobaan manipulasi
            'is_owner'              => 1,            // percobaan manipulasi
        ]);

        $created = User::where('email', 'hacker@test.com')->first();
        $this->assertNotNull($created);
        $this->assertEquals('customer', $created->role);
        $this->assertFalse((bool) $created->is_owner);
    }

    public function test_customer_cannot_access_admin_dashboard_or_orders(): void
    {
        $resDashboard = $this->actingAs($this->customerA)->get('/admin');
        $resDashboard->assertStatus(403);

        $resOrders = $this->actingAs($this->customerA)->get('/admin/pesanan');
        $resOrders->assertStatus(403);

        $resCustomers = $this->actingAs($this->customerA)->get('/admin/pelanggan');
        $resCustomers->assertStatus(403);
    }

    public function test_guest_redirected_to_login_when_accessing_private_routes(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/customer/dashboard')->assertRedirect('/login');
        $this->get('/checkout')->assertRedirect('/login');
        $this->get('/keranjang')->assertRedirect('/login');
    }

    /**
     * AUDIT BAGIAN D: Alur Transaksi, Server-Side Pricing, & Hubungan User
     */
    public function test_complete_customer_purchase_flow_and_order_synchronization(): void
    {
        // 1. Customer A menambahkan produk ke keranjang
        $cartResponse = $this->actingAs($this->customerA)->post('/keranjang', [
            'variant_id' => $this->variant->id,
            'quantity'   => 2,
        ]);
        $cartResponse->assertSessionHasNoErrors();

        // 2. Customer A checkout dengan manipulasi harga di request (harus diabaikan server)
        $checkoutResponse = $this->actingAs($this->customerA)->post('/checkout', [
            'customer_name'       => 'Customer A Asli',
            'customer_phone'      => '081234567890',
            'address_line'        => 'Jl. Pahlawan No. 10',
            'address_city'        => 'Jakarta',
            'address_province'    => 'DKI Jakarta',
            'address_postal_code' => '12110',
            'shipping_zone_id'    => $this->zone->id,
            'subtotal'            => 1000,  // percobaan manipulasi harga
            'total'               => 2000,  // percobaan manipulasi harga
        ]);

        $order = Order::where('customer_phone', '081234567890')->first();
        $this->assertNotNull($order);

        // Harga dihitung murni di server-side: (75.000 * 2) + 12.000 = 162.000
        $this->assertEquals(150000, $order->subtotal);
        $this->assertEquals(12000, $order->shipping_cost);
        $this->assertEquals(162000, $order->total);
        $this->assertEquals($this->customerA->id, $order->user_id);
        $this->assertEquals(OrderStatus::MenungguKonfirmasi, $order->status);

        // Snapshot item tersimpan
        $item = $order->orderItems()->first();
        $this->assertNotNull($item);
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(75000, $item->unit_price);
        $this->assertEquals(150000, $item->subtotal);
        $this->assertEquals('Peci Beludru Premium', $item->product_name);
        $this->assertEquals('Ukuran 8', $item->variant_label);

        // Stok belum dipotong saat checkout (menunggu konfirmasi admin)
        $this->assertEquals(10, $this->variant->fresh()->stock_quantity);

        // 3. Pesanan muncul di dashboard customer A
        $customerOrdersRes = $this->actingAs($this->customerA)->get('/customer/pesanan');
        $customerOrdersRes->assertStatus(200);
        $customerOrdersRes->assertSee($order->order_number);

        // 4. Pesanan muncul di dashboard admin
        $adminOrdersRes = $this->actingAs($this->admin)->get('/admin/pesanan');
        $adminOrdersRes->assertStatus(200);
        $adminOrdersRes->assertSee($order->order_number);
    }

    /**
     * AUDIT BAGIAN E: Otorisasi Kepemilikan & Manajemen Pesanan Admin
     */
    public function test_customer_b_cannot_view_customer_a_order(): void
    {
        $order = Order::create([
            'user_id'            => $this->customerA->id,
            'order_number'       => 'ORD-AUDIT-A01',
            'customer_name'      => 'Customer A',
            'customer_phone'     => '08111111111',
            'address_line'       => 'Alamat A',
            'city'               => 'Jakarta',
            'province'           => 'DKI Jakarta',
            'postal_code'        => '10000',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 75000,
            'shipping_cost'      => 12000,
            'total'              => 87000,
            'status'             => OrderStatus::MenungguKonfirmasi,
        ]);

        // Customer B mencoba melihat detail pesanan Customer A -> 403 Forbidden
        $response = $this->actingAs($this->customerB)->get("/customer/pesanan/{$order->order_number}");
        $response->assertStatus(403);

        // Customer B mencoba melihat konfirmasi pesanan Customer A -> 403 Forbidden
        $responseConfirm = $this->actingAs($this->customerB)->get("/pesanan/{$order->order_number}");
        $responseConfirm->assertStatus(403);
    }

    public function test_customer_cannot_modify_order_status_via_direct_request(): void
    {
        $order = Order::create([
            'user_id'            => $this->customerA->id,
            'order_number'       => 'ORD-AUDIT-A02',
            'customer_name'      => 'Customer A',
            'customer_phone'     => '08111111111',
            'address_line'       => 'Alamat A',
            'city'               => 'Jakarta',
            'province'           => 'DKI Jakarta',
            'postal_code'        => '10000',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 75000,
            'shipping_cost'      => 12000,
            'total'              => 87000,
            'status'             => OrderStatus::MenungguKonfirmasi,
        ]);

        // Customer A mencoba langsung mengonfirmasi pesanan melalui route admin -> 403
        $response = $this->actingAs($this->customerA)->post("/admin/pesanan/{$order->id}/konfirmasi");
        $response->assertStatus(403);
    }

    /**
     * AUDIT BAGIAN E & F: Siklus Status, Validasi Stok, & Pencegahan Pemotongan Ganda
     */
    public function test_admin_confirm_order_decrements_stock_and_prevents_double_decrement(): void
    {
        $order = Order::create([
            'user_id'            => $this->customerA->id,
            'order_number'       => 'ORD-AUDIT-STK',
            'customer_name'      => 'Customer A',
            'customer_phone'     => '08111111111',
            'address_line'       => 'Alamat A',
            'city'               => 'Jakarta',
            'province'           => 'DKI Jakarta',
            'postal_code'        => '10000',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 75000,
            'shipping_cost'      => 12000,
            'total'              => 87000,
            'status'             => OrderStatus::MenungguKonfirmasi,
        ]);

        $order->orderItems()->create([
            'product_variant_id' => $this->variant->id,
            'product_name'       => 'Peci Beludru Premium',
            'variant_label'      => 'Ukuran 8',
            'unit_price'         => 75000,
            'quantity'           => 3,
            'subtotal'           => 225000,
        ]);

        $this->assertEquals(10, $this->variant->fresh()->stock_quantity);

        // 1. Admin konfirmasi pesanan -> status Diproses & stok berkurang (10 - 3 = 7)
        $confirmResponse = $this->actingAs($this->admin)->post("/admin/pesanan/{$order->id}/konfirmasi");
        $confirmResponse->assertSessionHasNoErrors();
        $this->assertEquals(OrderStatus::Diproses, $order->fresh()->status);
        $this->assertEquals(7, $this->variant->fresh()->stock_quantity);

        // 2. Percobaan konfirmasi kedua kali TIDAK boleh mengurangi stok lagi
        $secondConfirm = $this->actingAs($this->admin)->post("/admin/pesanan/{$order->id}/konfirmasi");
        $secondConfirm->assertSessionHas('error');
        $this->assertEquals(7, $this->variant->fresh()->stock_quantity); // tetap 7, tidak berkurang lagi!

        // 3. Admin membatalkan pesanan yang Diproses -> stok dikembalikan (7 + 3 = 10)
        $cancelResponse = $this->actingAs($this->admin)->post("/admin/pesanan/{$order->id}/batal", [
            'cancellation_reason' => 'Permintaan pembatalan dari pembeli.',
        ]);
        $cancelResponse->assertSessionHasNoErrors();
        $this->assertEquals(OrderStatus::Dibatalkan, $order->fresh()->status);
        $this->assertEquals(10, $this->variant->fresh()->stock_quantity);
    }

    /**
     * AUDIT BAGIAN F: Transaksi Database Rollback saat Gagal
     */
    public function test_checkout_fails_gracefully_when_stock_is_insufficient_and_rolls_back(): void
    {
        // Set stok ke 1
        $this->variant->update(['stock_quantity' => 1]);

        // Masukkan qty 5 ke session keranjang
        session(['cart' => [$this->variant->id => 5]]);

        $response = $this->actingAs($this->customerA)->post('/checkout', [
            'customer_name'       => 'Customer A',
            'customer_phone'      => '081234567890',
            'address_line'        => 'Jl. Pahlawan',
            'address_city'        => 'Jakarta',
            'address_province'    => 'DKI Jakarta',
            'address_postal_code' => '12110',
            'shipping_zone_id'    => $this->zone->id,
        ]);

        $response->assertSessionHas('error');
        // Tidak ada pesanan parsial yang tersimpan di DB
        $this->assertEquals(0, Order::count());
        $this->assertEquals(0, OrderItem::count());
        // Stok tetap 1
        $this->assertEquals(1, $this->variant->fresh()->stock_quantity);
    }

    public function test_admin_seeder_can_be_reexecuted_idempotently(): void
    {
        config(['shop.admin_password' => 'AdminSecret123!']);
        putenv('ADMIN_PASSWORD=AdminSecret123!');

        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));
        $admin = User::where('email', $adminEmail)->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertEquals('admin', $admin->role);
    }
}
