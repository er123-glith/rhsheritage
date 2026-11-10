<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_product_image(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create([
            'email' => 'owner@rhs.test',
            'is_owner' => true,
        ]);

        $category = Category::create([
            'name' => 'Songkok',
            'slug' => 'songkok',
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Songkok Hitam Polos',
            'slug' => 'songkok-hitam-polos',
            'description' => 'Deskripsi songkok',
            'base_price' => 50000,
            'weight_gram' => 200,
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->image('test_product.jpg', 600, 600);

        $response = $this->actingAs($owner)
            ->post(route('admin.products.images.store', $product), [
                'images' => [$file],
                'alt_text' => 'Foto Songkok',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('product_images', 1);

        $image = $product->images()->first();
        $this->assertNotNull($image);
        Storage::disk('public')->assertExists($image->path);
    }
}
