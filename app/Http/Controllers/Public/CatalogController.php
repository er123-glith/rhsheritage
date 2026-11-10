<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'images' => fn($q) => $q->orderBy('sort_order')->limit(1),
            'variants' => fn($q) => $q->where('is_active', true)->orderBy('price'),
        ])->where('is_active', true);

        // Filter pencarian
        if ($search = $request->get('q')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter stok tersedia
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', fn($q) => $q->where('is_active', true)->where('stock_quantity', '>', 0));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('public.catalog.index', compact('products', 'categories'));
    }

    public function category(Request $request, Category $category)
    {
        $query = Product::with([
            'category',
            'images' => fn($q) => $q->orderBy('sort_order')->limit(1),
            'variants' => fn($q) => $q->where('is_active', true)->orderBy('price'),
        ])
            ->where('is_active', true)
            ->where('category_id', $category->id);

        if ($search = $request->get('q')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', fn($q) => $q->where('is_active', true)->where('stock_quantity', '>', 0));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('public.catalog.index', compact('products', 'categories', 'category'));
    }
}
