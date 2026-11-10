<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variantAttributes' => fn($q) => $q->orderBy('sort_order'),
            'variantAttributes.values' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->where('is_active', true)->orderBy('price'),
        ]);

        // Produk terkait dari kategori yang sama
        $relatedProducts = Product::with([
            'category',
            'images' => fn($q) => $q->orderBy('sort_order')->limit(1),
            'variants' => fn($q) => $q->where('is_active', true)->orderBy('price'),
        ])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('public.product.show', compact('product', 'relatedProducts'));
    }
}
