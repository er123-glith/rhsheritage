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
use App\Services\WhatsAppMessageBuilder;
use App\Support\Terbilang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAndWhatsAppRefactoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $otherCustomer;
    protected Order $order;
    protected ShippingZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'     => 'admin',
            'is_owner' => true,
            'email'    => 'owner-test@rhsheritage.example',
        ]);

        $this->customer = User::factory()->create([
            'role'  => 'customer',
            'email' => 'customer-test@rhsheritage.example',
        ]);

        $this->otherCustomer = User::factory()->create([
            'role'  => 'customer',
            'email' => 'other-customer@rhsheritage.example',
        ]);

        $this->zone = ShippingZone::create([
            'name'       => 'Zona 1 — Solo Raya',
            'base_rate'  => 10000,
            'is_active'  => true,
        ]);

        $category = Category::create([
            'name' => 'Batik Pria',
            'slug' => 'batik-pria',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Kemeja Batik Keris Parang',
            'slug'        => 'kemeja-batik-keris-parang',
            'description' => 'Batik cap katun primisima premium.',
            'base_price'  => 150000,
            'weight_gram' => 250,
            'is_active'   => true,
        ]);

        $variant = ProductVariant::create([
            'product_id'     => $product->id,
            'sku'            => 'KBK-P-L',
            'variant_label'  => 'Ukuran L',
            'attribute_map'  => ['Ukuran' => 'L'],
            'price'          => 150000,
            'stock_quantity' => 15,
            'is_active'      => true,
        ]);

        $this->order = Order::create([
            'order_number'       => 'RHS-20261114-TEST',
            'user_id'            => $this->customer->id,
            'customer_name'      => 'Ahmad Dahlan',
            'customer_phone'     => '081234567890',
            'address_line'       => 'Jl. Pahlawan Budaya No. 10',
            'city'               => 'Surakarta',
            'province'           => 'Jawa Tengah',
            'postal_code'        => '57126',
            'shipping_zone_id'   => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'subtotal'           => 300000,
            'shipping_cost'      => 10000,
            'total'              => 310000,
            'total_weight_gram'  => 500,
            'status'             => OrderStatus::MenungguKonfirmasi,
            'note'               => 'Tolong dipacking kardus rapi.',
        ]);

        OrderItem::create([
            'order_id'      => $this->order->id,
            'variant_id'    => $variant->id,
            'product_name'  => $product->name,
            'variant_label' => 'Ukuran: L',
            'quantity'      => 2,
            'unit_price'    => 150000,
            'subtotal'      => 300000,
            'weight_gram'   => 250,
        ]);
    }

    public function test_whatsapp_message_is_professional_and_has_no_json_block(): void
    {
        $builder = app(WhatsAppMessageBuilder::class);
        $message = $builder->buildMessage($this->order);

        // Pastikan tidak ada blok JSON sama sekali
        $this->assertStringNotContainsString('--- DATA JSON', $message);
        $this->assertStringNotContainsString('"order_number":', $message);
        $this->assertStringNotContainsString('payload_json', $message);

        // Pastikan tidak ada icon / emoji
        $this->assertStringNotContainsString('🌿', $message);
        $this->assertStringNotContainsString('📋', $message);
        $this->assertStringNotContainsString('👤', $message);
        $this->assertStringNotContainsString('📍', $message);
        $this->assertStringNotContainsString('🛍️', $message);
        $this->assertStringNotContainsString('💳', $message);
        $this->assertStringNotContainsString('🙏', $message);

        // Pastikan struktur pesan profesional lengkap
        $this->assertStringContainsString('RHS HERITAGE', $message);
        $this->assertStringContainsString('KONFIRMASI PESANAN', $message);
        $this->assertStringContainsString('RHS-20261114-TEST', $message);
        $this->assertStringContainsString('Ahmad Dahlan', $message);
        $this->assertStringContainsString('081234567890', $message);
        $this->assertStringContainsString('Kemeja Batik Keris Parang', $message);
        $this->assertStringContainsString('Rp300.000', $message);
        $this->assertStringContainsString('Rp10.000', $message);
        $this->assertStringContainsString('Rp310.000', $message);
        $this->assertStringContainsString('lacak-pesanan', $message);
    }

    public function test_whatsapp_short_message_is_clean_and_concise(): void
    {
        $builder = app(WhatsAppMessageBuilder::class);
        $short = $builder->buildShortMessage($this->order);

        $this->assertStringNotContainsString('json', strtolower($short));
        $this->assertStringContainsString('RHS-20261114-TEST', $short);
        $this->assertStringContainsString('Rp310.000', $short);
    }

    public function test_terbilang_helper_converts_rupiah_accurately(): void
    {
        $this->assertEquals('Dua Ratus Lima Puluh Ribu Rupiah', Terbilang::rupiah(250000));
        $this->assertEquals('Tiga Ratus Sepuluh Ribu Rupiah', Terbilang::rupiah(310000));
        $this->assertEquals('Satu Juta Lima Ratus Ribu Rupiah', Terbilang::rupiah(1500000));
    }

    public function test_admin_can_access_pdf_invoice_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.invoice', $this->order));

        $response->assertStatus(200);
        $response->assertSee('INVOICE');
        $response->assertSee('RHS-20261114-TEST');
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('Kemeja Batik Keris Parang');
        $response->assertSee('Tiga Ratus Sepuluh Ribu Rupiah');
        $response->assertSee('Cetak / Simpan PDF');
        $this->assertStringNotContainsString('fa-solid', $response->getContent());
        $this->assertStringNotContainsString('fa-regular', $response->getContent());
    }

    public function test_customer_can_access_their_own_pdf_invoice(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.orders.invoice', $this->order->order_number));

        $response->assertStatus(200);
        $response->assertSee('INVOICE');
        $response->assertSee('RHS-20261114-TEST');
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('Rp310.000');
    }

    public function test_customer_cannot_access_another_customers_pdf_invoice(): void
    {
        $response = $this->actingAs($this->otherCustomer)->get(route('customer.orders.invoice', $this->order->order_number));

        $response->assertStatus(403);
    }

    public function test_guest_is_prompted_or_forbidden_when_accessing_registered_customer_invoice_without_login(): void
    {
        $response = $this->get(route('order.invoice', $this->order->order_number));

        // Karena order terhubung ke akun customer terdaftar, tamu tanpa login harus diarahkan ke login
        $response->assertRedirect(route('login'));
    }

    public function test_admin_and_customer_show_pages_contain_invoice_link(): void
    {
        $adminResp = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order));
        $adminResp->assertStatus(200);
        $adminResp->assertSee(route('admin.orders.invoice', $this->order));

        $custResp = $this->actingAs($this->customer)->get(route('customer.orders.show', $this->order->order_number));
        $custResp->assertStatus(200);
        $custResp->assertSee(route('customer.orders.invoice', $this->order->order_number));
    }

    public function test_user_can_update_profile_info_with_address_fields(): void
    {
        $response = $this->actingAs($this->customer)->patch(route('profile.update'), [
            'name'        => 'Ahmad Dahlan Updated',
            'email'       => 'customer-updated@rhsheritage.example',
            'phone'       => '081299998888',
            'address'     => 'Jl. Kebangsaan Timur No. 45',
            'city'        => 'Yogyakarta',
            'province'    => 'D.I. Yogyakarta',
            'postal_code' => '55122',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $this->customer->refresh();
        $this->assertEquals('Ahmad Dahlan Updated', $this->customer->name);
        $this->assertEquals('081299998888', $this->customer->phone);
        $this->assertEquals('Jl. Kebangsaan Timur No. 45', $this->customer->address);
        $this->assertEquals('Yogyakarta', $this->customer->city);
        $this->assertEquals('Jl. Kebangsaan Timur No. 45, Yogyakarta, D.I. Yogyakarta, 55122', $this->customer->full_address);
    }

    public function test_admin_layout_renders_profile_modal_and_trigger_button(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('profileModalOpen');
        $response->assertSee('modal-profile-title');
        $response->assertSee('Ringkasan Profil');
        $response->assertSee('Ubah Informasi');
        $response->assertSee('Alamat Resmi Toko');
    }

    public function test_invoice_displays_admin_configured_store_address(): void
    {
        // Update admin address profile
        $this->admin->update([
            'phone'       => '085712345678',
            'address'     => 'Gallery Batik Utama, Jl. Ronggowarsito No. 100',
            'city'        => 'Surakarta',
            'province'    => 'Jawa Tengah',
            'postal_code' => '57131',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.invoice', $this->order));

        $response->assertStatus(200);
        $response->assertSee('Gallery Batik Utama, Jl. Ronggowarsito No. 100');
        $response->assertSee('Surakarta');
        $response->assertSee('085712345678');

        // Customer invoice juga melihat alamat toko admin yang terbaru
        $custResponse = $this->actingAs($this->customer)->get(route('customer.orders.invoice', $this->order->order_number));
        $custResponse->assertStatus(200);
        $custResponse->assertSee('Gallery Batik Utama, Jl. Ronggowarsito No. 100');
        $custResponse->assertSee('Surakarta');
        $custResponse->assertSee('085712345678');
    }
}
