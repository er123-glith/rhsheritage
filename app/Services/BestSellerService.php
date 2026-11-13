<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BestSellerService
{
    /**
     * Mengambil daftar produk terlaris (Best Seller) berdasarkan agregasi data transaksi riil.
     *
     * Aturan bisnis:
     * - Hanya menghitung transaksi dari pesanan yang sah (diproses, dikirim, selesai).
     * - Mengecualikan pesanan yang dibatalkan (dibatalkan) dan belum terkonfirmasi (menunggu_konfirmasi).
     * - Mengurangi kuantitas dari item yang telah disetujui retur/refund.
     * - Memastikan hanya produk aktif yang ditampilkan.
     * - Urut berdasarkan kuantitas bersih terjual (tertinggi), lalu nilai transaksi bersih, lalu product_id.
     *
     * @param int $limit
     * @return Collection
     */
    public function getBestSellers(int $limit = 6): Collection
    {
        $validStatuses = [
            OrderStatus::Diproses->value,
            OrderStatus::Dikirim->value,
            OrderStatus::Selesai->value,
        ];

        // 1. Ambil agregasi kuantitas terjual kotor per produk
        $grossSales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->whereIn('orders.status', $validStatuses)
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->select(
                'products.id as product_id',
                DB::raw('SUM(order_items.quantity) as gross_units_sold'),
                DB::raw('SUM(order_items.subtotal) as gross_revenue')
            )
            ->get()
            ->keyBy('product_id');

        if ($grossSales->isEmpty()) {
            return collect();
        }

        // 2. Ambil agregasi kuantitas yang diretur/refund untuk pesanan yang sah
        $returnedSales = DB::table('return_items')
            ->join('returns', 'return_items.return_id', '=', 'returns.id')
            ->join('product_variants', 'return_items.product_variant_id', '=', 'product_variants.id')
            ->whereIn('returns.status', [
                ReturnStatus::Disetujui->value,
                ReturnStatus::BarangDiperiksa->value,
                ReturnStatus::DalamPenyelesaian->value,
                ReturnStatus::Selesai->value,
            ])
            ->where('returns.solution', 'refund')
            ->groupBy('product_variants.product_id')
            ->select(
                'product_variants.product_id',
                DB::raw('SUM(return_items.quantity) as returned_units'),
                DB::raw('SUM(return_items.subtotal) as returned_revenue')
            )
            ->get()
            ->keyBy('product_id');

        // 3. Hitung penjualan bersih
        $netSales = [];
        foreach ($grossSales as $productId => $sale) {
            $returnedQty = $returnedSales->get($productId)->returned_units ?? 0;
            $returnedRev = $returnedSales->get($productId)->returned_revenue ?? 0;

            $netUnits = max(0, (int) $sale->gross_units_sold - (int) $returnedQty);
            $netRevenue = max(0, (float) $sale->gross_revenue - (float) $returnedRev);

            if ($netUnits > 0) {
                $netSales[$productId] = [
                    'product_id'   => $productId,
                    'units_sold'   => $netUnits,
                    'net_revenue'  => $netRevenue,
                ];
            }
        }

        if (empty($netSales)) {
            return collect();
        }

        // Urutkan berdasarkan unit terjual (desc), nilai penjualan (desc), product_id (asc)
        uasort($netSales, function ($a, $b) {
            if ($a['units_sold'] === $b['units_sold']) {
                if ($a['net_revenue'] === $b['net_revenue']) {
                    return $a['product_id'] <=> $b['product_id'];
                }
                return $b['net_revenue'] <=> $a['net_revenue'];
            }
            return $b['units_sold'] <=> $a['units_sold'];
        });

        $topSales = array_slice($netSales, 0, $limit, true);
        $topProductIds = array_keys($topSales);

        // 4. Eager load Product models lengkap untuk mencegah N+1
        $products = Product::with([
                'category',
                'images',
                'primaryImage',
                'variants' => fn($q) => $q->where('is_active', true)->orderBy('price'),
            ])
            ->whereIn('id', $topProductIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Pertahankan urutan ranking penjualan
        $orderedProducts = collect();
        $rank = 1;
        foreach ($topSales as $productId => $meta) {
            if ($products->has($productId)) {
                $product = $products->get($productId);
                $product->total_sold = $meta['units_sold'];
                $product->net_revenue = $meta['net_revenue'];
                $product->sales_rank = $rank++;
                $orderedProducts->push($product);
            }
        }

        return $orderedProducts;
    }
}
