<?php

namespace Tests\Feature\Public;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $variant;
    private ShippingZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Songkok',
            'slug' => 'songkok',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Hitam Super',
            'slug' => 'songkok-hitam-super',
            'base_price' => 50000,
            'weight_gram' => 150,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SKK-SUPER-07',
            'variant_label' => 'No. 7',
            'attribute_map' => ['Ukuran' => 'No. 7'],
            'price' => 48000,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $this->zone = ShippingZone::create([
            'name' => 'Pulau Jawa',
            'base_rate' => 10000,
            'rate_per_extra_kg' => 5000,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_place_order_and_is_redirected_to_login(): void
    {
        $response = $this->withSession([
            'cart' => [
                $this->variant->id => 1,
            ],
        ])->post('/checkout', [
            'customer_name' => 'Rahmat Saputra',
            'customer_phone' => '081213338952',
            'address_line' => 'Jl. Merdeka No. 45',
            'address_city' => 'Jakarta Selatan',
            'address_province' => 'DKI Jakarta',
            'address_postal_code' => '12345',
            'shipping_zone_id' => $this->zone->id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertNull(Order::first());
    }

    public function test_can_place_order_successfully(): void
    {
        $customer = \App\Models\User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->withSession([
            'cart' => [
                $this->variant->id => 1,
            ],
        ])->post('/checkout', [
            'customer_name' => 'Rahmat Saputra',
            'customer_phone' => '081213338952',
            'address_line' => 'Jl. Merdeka No. 45',
            'address_city' => 'Jakarta Selatan',
            'address_province' => 'DKI Jakarta',
            'address_postal_code' => '12345',
            'address_note' => 'Pagar warna hitam',
            'shipping_zone_id' => $this->zone->id,
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('Rahmat Saputra', $order->customer_name);
        $this->assertEquals('081213338952', $order->customer_phone);
        $this->assertEquals('Jakarta Selatan', $order->city);
        $this->assertEquals('DKI Jakarta', $order->province);
        $this->assertEquals('12345', $order->postal_code);
        $this->assertEquals('Pagar warna hitam', $order->note);
        $this->assertEquals('Pulau Jawa', $order->shipping_zone_name);
        $this->assertEquals(OrderStatus::MenungguKonfirmasi, $order->status);
        $this->assertEquals(48000, $order->subtotal);
        $this->assertEquals(10000, $order->shipping_cost);
        $this->assertEquals(58000, $order->total);

        $response->assertRedirect(route('order.confirmation', $order->order_number));
    }
}
