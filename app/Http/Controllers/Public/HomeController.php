<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Services\BestSellerService;

class HomeController extends Controller
{
    public function __construct(
        protected BestSellerService $bestSellerService
    ) {}

    public function index()
    {
        $baseQuery = fn() => Product::with(['category', 'images', 'primaryImage', 'variants' => fn($q) => $q->where('is_active', true)->orderBy('price')])
            ->where('is_active', true)
            ->whereHas('variants', fn($q) => $q->where('is_active', true)->where('stock_quantity', '>', 0));

        // 1. Produk Terlaris (Best Seller) dihitung dari transaksi aktual database
        $bestSellerProducts = $this->bestSellerService->getBestSellers(6);

        // Fallback jika belum ada transaksi untuk section alternatif tanpa klaim palsu
        $fallbackCatalogProducts = $bestSellerProducts->isEmpty()
            ? $baseQuery()->latest()->take(6)->get()
            : collect();

        // 2. Kategori Produk
        $categories = Category::withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        // 3. Rekomendasi Feed
        $featuredProducts = $baseQuery()->latest()->take(12)->get();
        $songkokProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'songkok'))->take(8)->get();
        $sarungProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'sarung'))->take(8)->get();
        $jilbabProducts = $baseQuery()->whereHas('category', fn($q) => $q->where('slug', 'jilbab'))->take(8)->get();

        // 4. Ulasan & Kepercayaan Pelanggan aktual dari database
        $publishedReviews = Review::published()
            ->with(['user', 'order'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $reviewCount = Review::published()->count();
        $averageRating = $reviewCount > 0 ? round((float) Review::published()->avg('rating'), 1) : 0.0;

        return view('public.home', compact(
            'bestSellerProducts',
            'fallbackCatalogProducts',
            'featuredProducts',
            'categories',
            'songkokProducts',
            'sarungProducts',
            'jilbabProducts',
            'publishedReviews',
            'reviewCount',
            'averageRating'
        ));
    }
}
