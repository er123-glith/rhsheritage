<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $threshold = config('shop.low_stock_threshold', 5);

        // Ringkasan KPI Stok
        $totalVariants = ProductVariant::count();
        $outOfStockCount = ProductVariant::where('stock_quantity', '<=', 0)->count();
        $lowStockCount = ProductVariant::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', $threshold)
            ->count();
        $inStockCount = ProductVariant::where('stock_quantity', '>', $threshold)->count();

        // Query varian
        $query = ProductVariant::with(['product.category', 'product.primaryImage']);

        // Filter Pencarian (nama produk, SKU, label varian)
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('variant_label', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Kategori
        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('product', function ($pq) use ($categoryId) {
                $pq->where('category_id', $categoryId);
            });
        }

        // Filter Kondisi Stok
        $stockFilter = $request->input('stock_status');
        if ($stockFilter === 'low_stock') {
            $query->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', $threshold);
        } elseif ($stockFilter === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($stockFilter === 'in_stock') {
            $query->where('stock_quantity', '>', $threshold);
        }

        $variants = $query->orderBy('stock_quantity', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.stock.index', compact(
            'variants',
            'categories',
            'threshold',
            'totalVariants',
            'outOfStockCount',
            'lowStockCount',
            'inStockCount'
        ));
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:999999'],
        ]);

        $variant->update([
            'stock_quantity' => $validated['stock_quantity'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stok untuk {$variant->variant_label} berhasil diperbarui menjadi {$variant->stock_quantity}.",
                'stock_quantity' => $variant->stock_quantity,
            ]);
        }

        return back()->with('status', "Stok varian \"{$variant->product->name} - {$variant->variant_label}\" berhasil diperbarui menjadi {$variant->stock_quantity}.");
    }

    public function quickAdjust(Request $request, ProductVariant $variant): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'delta' => ['required', 'integer', 'between:-1000,1000'],
        ]);

        $newStock = max(0, $variant->stock_quantity + (int)$validated['delta']);
        $variant->update(['stock_quantity' => $newStock]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Stok berhasil disesuaikan.",
                'stock_quantity' => $variant->stock_quantity,
            ]);
        }

        return back()->with('status', "Stok varian \"{$variant->product->name} - {$variant->variant_label}\" berhasil disesuaikan menjadi {$newStock}.");
    }
}
