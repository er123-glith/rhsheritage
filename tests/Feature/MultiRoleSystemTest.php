<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiRoleSystemTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Product $product;
    private ProductVariant $variant;
    private ShippingZone $zone;
    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Data Kategori & Produk
        $this->category = Category::create([
            'name'       => 'Songkok Nusantara',
            'slug'       => 'songkok',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Songkok Exclusive Beludru',
            'slug'        => 'songkok-exclusive-beludru',
            'base_price'  => 85000,
            'weight_gram' => 200,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'     => $this->product->id,
            'sku'            => 'SKK-EXC-08',
            'variant_label'  => 'Ukuran 8',
            'attribute_map'  => ['Ukuran' => '8'],
            'price'          => 85000,
            'stock_quantity' => 15,
            'is_active'      => true,
        ]);

        $this->zone = ShippingZone::create([
            'name'              => 'Pulau Jawa',
            'base_rate'         => 12000,
            'rate_per_extra_kg' => 5000,
            'sort_order'        => 1,
            'is_active'         => true,
        ]);

        // 2. Akun Admin (Owner)
        $this->admin = User::create([
            'name'     => 'Owner RHS Heritage',
            'email'    => 'owner@rhsheritage.example',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        // 3. Akun Customer
        $this->customer = User::create([
            'name'     => 'Pelanggan Setia',
            'email'    => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'role'     => 'customer',
            'is_owner' => false,
        ]);
    }

    /** Skenario 1: Guest dapat membuka landing page */
    public function test_skenario_01_guest_can_access_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('RHS HERITAGE');
        $response->assertSee('Masuk');
        $response->assertSee('Daftar');
    }

    /** Skenario 2: Guest dapat melihat katalog produk */
    public function test_skenario_02_guest_can_access_catalog(): void
    {
        $response = $this->get('/katalog');
        $response->assertStatus(200);
        $response->assertSee('Songkok Exclusive Beludru');
    }

    /** Skenario 3: Guest dapat melihat detail produk */
    public function test_skenario_03_guest_can_access_product_detail(): void
    {
        $response = $this->get('/produk/' . $this->product->slug);
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('Tambah ke Keranjang');
    }

    /** Skenario 4: Guest tidak dapat menambahkan produk ke keranjang */
    public function test_skenario_04_guest_cannot_add_item_to_cart(): void
    {
        $response = $this->from('/produk/' . $this->product->slug)->post('/keranjang', [
            'variant_id' => $this->variant->id,
            'quantity'   => 1,
        ]);

        // Tidak boleh bertambah di session
        $this->assertNull(session('cart'));
    }

    /** Skenario 5: Guest diarahkan ke login saat mencoba fitur transaksi */
    public function test_skenario_05_guest_is_redirected_to_login_on_transaction(): void
    {
        $response = $this->from('/produk/' . $this->product->slug)->post('/keranjang', [
            'variant_id' => $this->variant->id,
            'quantity'   => 1,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('cart_auth_notice', 'Silakan login atau daftar terlebih dahulu untuk menambahkan produk ke keranjang.');

        // Cek juga proteksi checkout untuk guest
        $checkoutResponse = $this->get('/checkout');
        $checkoutResponse->assertRedirect(route('login'));
    }

    /** Skenario 6: Guest dapat membuka halaman registrasi */
    public function test_skenario_06_guest_can_access_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Pelanggan');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Alamat Email');
    }

    /** Skenario 7: Registrasi baru menghasilkan role customer */
    public function test_skenario_07_registration_assigns_customer_role(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Budi Baru',
            'email'                 => 'budi.baru@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'budi.baru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertFalse((bool) $user->is_owner);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAdmin());
    }

    /** Skenario 8: Registrasi tidak dapat menghasilkan role admin (input manipulatif) */
    public function test_skenario_08_registration_cannot_produce_admin_via_injected_input(): void
    {
        $this->post('/register', [
            'name'                  => 'Hacker Impostor',
            'email'                 => 'hacker@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin',
            'is_owner'              => true,
        ]);

        $user = User::where('email', 'hacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertFalse((bool) $user->is_owner);
        $this->assertFalse($user->isAdmin());
    }

    /** Skenario 9: Customer berhasil login */
    public function test_skenario_09_customer_can_login_successfully(): void
    {
        $response = $this->post('/login', [
            'email'    => 'customer@gmail.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->customer);
        $response->assertRedirect('/dashboard'); // dashboard me-redirect customer ke home
    }

    /** Skenario 10: Owner berhasil login sebagai admin */
    public function test_skenario_10_owner_can_login_as_admin(): void
    {
        $response = $this->post('/login', [
            'email'    => 'owner@rhsheritage.example',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->admin);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue($this->admin->isAdmin());
    }

    /** Skenario 11: Customer tidak dapat mengakses dashboard admin (403) */
    public function test_skenario_11_customer_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin');
        $response->assertStatus(403);

        $productManageResponse = $this->actingAs($this->customer)->get('/admin/produk');
        $productManageResponse->assertStatus(403);
    }

    /** Skenario 12: Customer dapat menambahkan produk ke keranjang */
    public function test_skenario_12_customer_can_add_item_to_cart(): void
    {
        $response = $this->actingAs($this->customer)->post('/keranjang', [
            'variant_id' => $this->variant->id,
            'quantity'   => 2,
        ]);

        $response->assertSessionHas('success', 'Produk berhasil ditambahkan ke keranjang.');
        $cart = session('cart');
        $this->assertEquals(2, $cart[$this->variant->id]);
    }

    /** Skenario 13: Customer dapat mengubah isi keranjangnya */
    public function test_skenario_13_customer_can_update_cart_quantity(): void
    {
        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => [$this->variant->id => 2]])
            ->patch('/keranjang/' . $this->variant->id, [
                'quantity' => 5,
            ]);

        $response->assertSessionHas('success', 'Jumlah berhasil diperbarui.');
        $cart = session('cart');
        $this->assertEquals(5, $cart[$this->variant->id]);
    }

    /** Skenario 14: Customer dapat melakukan checkout dan pesanan terhubung dengan user_id */
    public function test_skenario_14_customer_can_checkout_and_order_has_user_id(): void
    {
        $response = $this->actingAs($this->customer)
            ->withSession(['cart' => [$this->variant->id => 1]])
            ->post('/checkout', [
                'customer_name'       => 'Pelanggan Setia',
                'customer_phone'      => '081234567890',
                'address_line'        => 'Jl. Diponegoro No. 10',
                'address_city'        => 'Surabaya',
                'address_province'    => 'Jawa Timur',
                'address_postal_code' => '60241',
                'shipping_zone_id'    => $this->zone->id,
            ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->customer->id, $order->user_id);
        $this->assertEquals(OrderStatus::MenungguKonfirmasi, $order->status);
        $response->assertRedirect(route('order.confirmation', $order->order_number));
    }

    /** Skenario 15: Customer tidak dapat melihat pesanan customer lain (403) */
    public function test_skenario_15_customer_cannot_view_another_customers_order(): void
    {
        $otherCustomer = User::create([
            'name'     => 'Orang Lain',
            'email'    => 'other@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'customer',
        ]);

        $otherOrder = Order::create([
            'user_id'            => $otherCustomer->id,
            'order_number'       => 'RHS-TEST-9999',
            'customer_name'      => 'Orang Lain',
            'customer_phone'     => '08999999999',
            'address_line'       => 'Jl. Rahasia',
            'city'               => 'Bandung',
            'province'           => 'Jawa Barat',
            'postal_code'        => '40111',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram'  => 200,
            'subtotal'           => 85000,
            'shipping_cost'      => 12000,
            'total'              => 97000,
            'status'             => OrderStatus::MenungguKonfirmasi,
        ]);

        // Customer pertama mencoba melihat pesanan milik $otherCustomer
        $response = $this->actingAs($this->customer)->get('/pesanan/' . $otherOrder->order_number);
        $response->assertStatus(403);

        // Tetapi pemilik pesanan dapat melihatnya
        $ownerResponse = $this->actingAs($otherCustomer)->get('/pesanan/' . $otherOrder->order_number);
        $ownerResponse->assertStatus(200);

        // Dan Admin juga dapat melihatnya
        $adminResponse = $this->actingAs($this->admin)->get('/pesanan/' . $otherOrder->order_number);
        $adminResponse->assertStatus(200);
    }

    /** Skenario 16: Admin tetap dapat mengelola produk */
    public function test_skenario_16_admin_can_manage_products(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
        $response->assertSee('Kelola katalog, foto, dan varian produk');
    }

    /** Skenario 17: Admin tetap dapat mengelola pesanan */
    public function test_skenario_17_admin_can_manage_orders(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pesanan');
        $response->assertStatus(200);
        $response->assertSee('Pesanan');
    }

    /** Skenario 18: Logout berhasil mengakhiri sesi pengguna */
    public function test_skenario_18_logout_ends_user_session(): void
    {
        $response = $this->actingAs($this->customer)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    /** Skenario 19: Redirect setelah login berjalan sesuai role */
    public function test_skenario_19_login_redirect_works_by_role(): void
    {
        // Admin diarahkan ke dashboard admin
        $adminLogin = $this->post('/login', [
            'email'    => 'owner@rhsheritage.example',
            'password' => 'password123',
        ]);
        $adminLogin->assertRedirect(route('admin.dashboard'));

        $this->post('/logout');

        // Customer diarahkan ke intended / home
        $customerLogin = $this->post('/login', [
            'email'    => 'customer@gmail.com',
            'password' => 'password123',
        ]);
        $customerLogin->assertRedirect('/dashboard');
    }

    /** Skenario 20: Tampilan navigasi dinamis sesuai status pengguna */
    public function test_skenario_20a_guest_navigation(): void
    {
        $guestView = $this->get('/');
        $guestView->assertSee('Masuk');
        $guestView->assertSee('Daftar');
        $guestView->assertDontSee('Pesanan Saya');
    }

    public function test_skenario_20b_customer_navigation(): void
    {
        $customerView = $this->actingAs($this->customer)->get('/');
        $customerView->assertSee('Pesanan Saya');
        $customerView->assertSee('Profil');
        $customerView->assertSee('Keluar');
        $customerView->assertDontSee('Panel Admin');
    }

    public function test_skenario_20c_admin_navigation(): void
    {
        $adminView = $this->actingAs($this->admin)->get('/');
        $adminView->assertSee('Panel Admin');
        $adminView->assertSee('Keluar');
    }

    /** Skenario 21: Data lama dan integritas pengguna tetap terjaga */
    public function test_skenario_21_existing_data_integrity_preserved(): void
    {
        $this->assertDatabaseHas('users', [
            'email'    => 'owner@rhsheritage.example',
            'role'     => 'admin',
            'is_owner' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'slug' => 'songkok-exclusive-beludru',
        ]);
    }

    /** Skenario 22: Tidak ada error baru yang berasal dari autentikasi */
    public function test_skenario_22_no_unexpected_errors_in_application_flow(): void
    {
        $response = $this->get('/tentang');
        $response->assertStatus(200);

        $grosirResponse = $this->get('/grosir-mitra');
        $grosirResponse->assertStatus(200);

        $trackResponse = $this->get('/lacak-pesanan');
        $trackResponse->assertStatus(200);
    }
}
