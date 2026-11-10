<?php

namespace Tests\Feature\Admin;

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

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private ProductVariant $variant;
    private ShippingZone $zone;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['is_owner' => true]);

        $category = Category::create([
            'name' => 'Songkok',
            'slug' => 'songkok',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Hitam',
            'slug' => 'songkok-hitam',
            'base_price' => 75000,
            'weight_gram' => 200,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKK-01',
            'variant_label' => 'No. 7',
            'attribute_map' => ['Ukuran' => 'No. 7'],
            'price' => 75000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->zone = ShippingZone::create([
            'name' => 'Zona Surabaya',
            'base_rate' => 10000,
            'rate_per_extra_kg' => 5000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'address_line' => 'Jl. Pemuda No. 1',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'postal_code' => '60271',
            'shipping_zone_id' => $this->zone->id,
            'shipping_zone_name' => $this->zone->name,
            'total_weight_gram' => 200,
            'subtotal' => 150000,
            'shipping_cost' => 10000,
            'total' => 160000,
            'status' => OrderStatus::MenungguKonfirmasi,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_variant_id' => $this->variant->id,
            'product_name' => 'Songkok Hitam',
            'variant_label' => 'No. 7',
            'unit_price' => 75000,
            'quantity' => 2,
            'subtotal' => 150000,
        ]);
    }

    public function test_owner_can_view_orders_index(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/pesanan');

        $response->assertStatus(200);
        $response->assertSee('Daftar Pesanan Masuk');
        $response->assertSee('ORD-TEST-001');
        $response->assertSee('Budi Santoso');
    }

    public function test_owner_can_view_order_detail(): void
    {
        $response = $this->actingAs($this->owner)->get("/admin/pesanan/{$this->order->id}");

        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-001');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Chat Pelanggan di WhatsApp');
    }

    public function test_confirming_order_reduces_stock_and_transitions_to_diproses(): void
    {
        $this->assertEquals(10, $this->variant->stock_quantity);

        $response = $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/konfirmasi");

        $response->assertRedirect();
        $this->order->refresh();
        $this->variant->refresh();

        $this->assertEquals(OrderStatus::Diproses, $this->order->status);
        $this->assertNotNull($this->order->confirmed_at);
        $this->assertEquals(8, $this->variant->stock_quantity); // 10 - 2

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $this->order->id,
            'to_status' => OrderStatus::Diproses->value,
        ]);
    }

    public function test_confirming_order_fails_when_stock_is_insufficient(): void
    {
        // Ubah stok menjadi 1 sedangkan pesanan minta 2
        $this->variant->update(['stock_quantity' => 1]);

        $response = $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/konfirmasi");

        $response->assertSessionHas('error');
        $this->order->refresh();
        $this->assertEquals(OrderStatus::MenungguKonfirmasi, $this->order->status);
    }

    public function test_canceling_processed_order_restores_stock(): void
    {
        // Konfirmasi dulu
        $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/konfirmasi");
        $this->variant->refresh();
        $this->assertEquals(8, $this->variant->stock_quantity);

        // Batalkan pesanan
        $response = $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/batal", [
            'cancellation_reason' => 'Stok kain habis dari pabrik',
        ]);

        $response->assertRedirect();
        $this->order->refresh();
        $this->variant->refresh();

        $this->assertEquals(OrderStatus::Dibatalkan, $this->order->status);
        $this->assertEquals('Stok kain habis dari pabrik', $this->order->cancellation_reason);
        $this->assertEquals(10, $this->variant->stock_quantity); // Stok dikembalikan ke 10
    }

    public function test_owner_can_mark_order_as_shipped_with_tracking_number(): void
    {
        // Konfirmasi dulu agar status menjadi Diproses
        $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/konfirmasi");

        // Kirim
        $response = $this->actingAs($this->owner)->post("/admin/pesanan/{$this->order->id}/status", [
            'target_status' => OrderStatus::Dikirim->value,
            'tracking_number' => 'JNE12345678',
        ]);

        $response->assertRedirect();
        $this->order->refresh();

        $this->assertEquals(OrderStatus::Dikirim, $this->order->status);
        $this->assertEquals('JNE12345678', $this->order->tracking_number);
    }
}
