<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_page_loads_with_related_products_without_lazy_loading_error(): void
    {
        $category = Category::create([
            'name' => 'Songkok',
            'slug' => 'songkok',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Motif AC',
            'slug' => 'songkok-motif-ac',
            'description' => 'Test description',
            'base_price' => 80000,
            'weight_gram' => 200,
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product1->id,
            'variant_label' => 'Hitam / No. 8',
            'attribute_map' => ['Warna' => 'Hitam', 'Ukuran' => '8'],
            'sku' => 'SKU-001',
            'price' => 80000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Polos',
            'slug' => 'songkok-polos',
            'description' => 'Related product',
            'base_price' => 75000,
            'weight_gram' => 200,
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product2->id,
            'variant_label' => 'Hitam / No. 7',
            'attribute_map' => ['Warna' => 'Hitam', 'Ukuran' => '7'],
            'sku' => 'SKU-002',
            'price' => 75000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $response = $this->get(route('product.show', $product1->slug));

        $response->assertStatus(200);
        $response->assertSee('Songkok Motif AC');
        $response->assertSee('Songkok Polos');
    }
}
