<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $baseQuery = fn() => Product::with(['category', 'images', 'primaryImage', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('price')])
            ->where('is_active', true)
            ->whereHas('variants', fn($q) => $q->where('is_active', true)->where('stock_quantity', '>', 0));

        $featuredProducts = $baseQuery()->latest()->take(12)->get();
        $flashSaleProducts = $baseQuery()->inRandomOrder()->take(6)->get();

        $categories = Category::withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $songkokProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'songkok'))->take(8)->get();
        $sarungProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'sarung'))->take(8)->get();
        $jilbabProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'jilbab'))->take(8)->get();

        return view('public.home', compact(
            'featuredProducts',
            'flashSaleProducts',
            'categories',
            'songkokProducts',
            'sarungProducts',
            'jilbabProducts'
        ));
    }
}
