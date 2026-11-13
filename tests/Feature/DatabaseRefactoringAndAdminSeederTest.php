<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseRefactoringAndAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan seluruh tabel Laravel yang diperlukan terbentuk dan tidak ada tabel WordPress.
     */
    public function test_all_required_tables_exist_and_no_wordpress_tables_present(): void
    {
        $requiredTables = [
            'users',
            'password_reset_tokens',
            'sessions',
            'categories',
            'products',
            'product_images',
            'variant_attributes',
            'attribute_values',
            'product_variants',
            'shipping_zones',
            'orders',
            'order_items',
            'order_status_logs',
            'partner_applications',
            'page_contents',
            'cache',
            'jobs',
        ];

        foreach ($requiredTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Tabel yang dibutuhkan '{$table}' tidak ditemukan pada database."
            );
        }

        // Pastikan tabel users memiliki kolom role dan is_owner
        $this->assertTrue(Schema::hasColumn('users', 'role'));
        $this->assertTrue(Schema::hasColumn('users', 'is_owner'));

        // Pastikan tabel orders memiliki kolom user_id
        $this->assertTrue(Schema::hasColumn('orders', 'user_id'));

        // Verifikasi tidak ada tabel berawalan wp_ (WordPress)
        $allTables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        foreach ($allTables as $t) {
            $this->assertFalse(
                str_starts_with($t->name, 'wp_'),
                "Ditemukan tabel WordPress '{$t->name}' yang tidak boleh ada pada database aplikasi."
            );
        }
    }

    /**
     * Memastikan AdminUserSeeder membuat akun admin dengan benar dari konfigurasi environment.
     */
    public function test_admin_seeder_creates_administrator_account_with_hashed_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));

        $admin = User::where('email', $adminEmail)->first();

        $this->assertNotNull($admin, "Akun admin dengan email '{$adminEmail}' tidak ditemukan.");
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->is_owner);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCustomer());
        $this->assertNotNull($admin->email_verified_at);

        // Password harus di-hash (Bcrypt / Argon)
        $this->assertNotEquals(env('ADMIN_PASSWORD'), $admin->password);
        $this->assertTrue(Hash::check(env('ADMIN_PASSWORD'), $admin->password));
    }

    /**
     * Memastikan seeder bersifat idempotent (tidak duplikat & tidak merusak password existing saat dijalankan ulang).
     */
    public function test_admin_seeder_is_idempotent_and_does_not_duplicate_or_overwrite_password(): void
    {
        $this->seed(AdminUserSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));
        $originalHash = User::where('email', $adminEmail)->value('password');

        // Jalankan seeder kedua kali
        $this->seed(AdminUserSeeder::class);

        // Pastikan jumlah user dengan email tersebut tetap 1
        $this->assertEquals(1, User::where('email', $adminEmail)->count());

        // Pastikan password hash tidak berubah
        $newHash = User::where('email', $adminEmail)->value('password');
        $this->assertEquals($originalHash, $newHash);
    }

    /**
     * Memastikan seeder menolak jika email admin sudah digunakan oleh akun customer.
     */
    public function test_admin_seeder_does_not_escalate_customer_to_admin_on_email_conflict(): void
    {
        $adminEmail = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));

        // Buat akun customer terlebih dahulu dengan email admin
        User::create([
            'name' => 'Customer Biasa',
            'email' => $adminEmail,
            'password' => Hash::make('secretCustomer123'),
            'role' => 'customer',
            'is_owner' => false,
        ]);

        // Jalankan seeder admin
        $this->seed(AdminUserSeeder::class);

        // Akun customer tersebut TIDAK BOLEH berubah menjadi admin secara diam-diam
        $user = User::where('email', $adminEmail)->first();
        $this->assertEquals('customer', $user->role);
        $this->assertFalse($user->is_owner);
    }

    /**
     * Memastikan Admin dapat login dan mengakses dashboard admin.
     */
    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $this->seed(AdminUserSeeder::class);

        $adminEmail = env('ADMIN_EMAIL', config('shop.admin_email', 'admin@rhsheritage.id'));

        $loginResponse = $this->post(route('login'), [
            'email' => $adminEmail,
            'password' => env('ADMIN_PASSWORD'),
        ]);

        $this->assertAuthenticated();
        $loginResponse->assertRedirect(route('admin.dashboard'));

        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('PANEL OWNER');
    }

    /**
     * Memastikan Customer mendaftar publik otomatis mendapatkan role customer dan tidak bisa akses panel admin.
     */
    public function test_customer_registration_assigns_customer_role_and_cannot_access_admin(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Pelanggan Baru',
            'email' => 'customerbaru@example.com',
            'password' => 'Rahasia12345!',
            'password_confirmation' => 'Rahasia12345!',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'customerbaru@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertFalse($user->is_owner);
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isCustomer());

        // Coba akses panel admin -> harus 403 Forbidden
        $adminAccessResponse = $this->get(route('admin.dashboard'));
        $adminAccessResponse->assertStatus(403);
    }

    /**
     * Memastikan relasi database Category -> Product -> Variant -> OrderItem -> Order berfungsi valid.
     */
    public function test_database_relationships_function_consistently(): void
    {
        $category = Category::create([
            'name' => 'Songkok Nusantara',
            'slug' => 'songkok-nusantara',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Songket Eksklusif',
            'slug' => 'songkok-songket-eksklusif',
            'description' => 'Songkok berkualitas premium.',
            'base_price' => 125000,
            'weight_gram' => 250,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKU-SONG-09',
            'variant_label' => 'Ukuran 9 - Tinggi 9 cm',
            'attribute_map' => ['ukuran' => '9', 'tinggi' => '9'],
            'price' => 125000,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        $zone = ShippingZone::create([
            'name' => 'Jawa Timur',
            'base_rate' => 10000,
            'rate_per_extra_kg' => 5000,
            'is_active' => true,
        ]);

        $customer = User::create([
            'name' => 'Pembeli Kriya',
            'email' => 'pembeli@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'RHS-TEST-RELASI-01',
            'customer_name' => 'Pembeli Kriya',
            'customer_phone' => '081234567890',
            'address_line' => 'Jl. Pahlawan No. 45',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'postal_code' => '60175',
            'shipping_zone_id' => $zone->id,
            'shipping_zone_name' => $zone->name,
            'total_weight_gram' => 250,
            'subtotal' => 125000,
            'shipping_cost' => 10000,
            'total' => 135000,
            'status' => 'menunggu_konfirmasi',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_label' => $variant->variant_label,
            'unit_price' => $variant->price,
            'quantity' => 1,
            'subtotal' => 125000,
        ]);

        // Verifikasi relasi
        $this->assertEquals($category->id, $product->category->id);
        $this->assertEquals($product->id, $variant->product->id);
        $this->assertEquals($customer->id, $order->user->id);
        $this->assertEquals(1, $order->items->count());
        $this->assertEquals($variant->id, $order->items->first()->variant->id);
    }
}
