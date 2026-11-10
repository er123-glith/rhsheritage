<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Product $product;
    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['is_owner' => true]);

        $category = Category::create([
            'name' => 'Songkok',
            'slug' => 'songkok',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Nasional Beludru Hitam AC',
            'slug' => 'songkok-nasional-beludru-hitam-ac',
            'base_price' => 75000,
            'weight_gram' => 200,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'SKK-AC-07',
            'variant_label' => 'No. 7 (Tinggi 9cm)',
            'attribute_map' => ['Ukuran' => 'No. 7', 'Tinggi' => '9cm'],
            'price' => 75000,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);
    }

    public function test_owner_can_view_stock_index(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin/stok');

        $response->assertStatus(200);
        $response->assertSee('Kelola Stok Inventaris');
        $response->assertSee('Songkok Nasional Beludru Hitam AC');
        $response->assertSee('SKK-AC-07');
    }

    public function test_owner_can_update_stock_quantity(): void
    {
        $response = $this->actingAs($this->owner)->patch("/admin/stok/{$this->variant->id}", [
            'stock_quantity' => 35,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_variants', [
            'id' => $this->variant->id,
            'stock_quantity' => 35,
        ]);
    }

    public function test_owner_can_quick_adjust_stock(): void
    {
        $response = $this->actingAs($this->owner)->patch("/admin/stok/{$this->variant->id}/quick", [
            'delta' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_variants', [
            'id' => $this->variant->id,
            'stock_quantity' => 20,
        ]);
    }

    public function test_stock_cannot_be_negative(): void
    {
        $response = $this->actingAs($this->owner)->patch("/admin/stok/{$this->variant->id}", [
            'stock_quantity' => -5,
        ]);

        $response->assertSessionHasErrors('stock_quantity');
    }
}
