<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PartnerApplication;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard ringkasan admin dengan seluruh statistik aktual dari database.
     * Mengikuti spesifikasi Section D.1 (tanpa angka acak, mock, atau data dummy).
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            // Statistik Pesanan
            'pendingOrdersCount'    => Order::where('status', OrderStatus::MenungguKonfirmasi)->count(),
            'processingOrdersCount' => Order::where('status', OrderStatus::Diproses)->count(),
            'completedOrdersCount'  => Order::where('status', OrderStatus::Selesai)->count(),
            'cancelledOrdersCount'  => Order::where('status', OrderStatus::Dibatalkan)->count(),
            'totalOrdersCount'      => Order::count(),

            // Finansial: Pendapatan dari transaksi yang selesai/terbayar
            'totalRevenue'          => (float) Order::where('status', OrderStatus::Selesai)->sum('total'),
            'totalOrderValue'       => (float) Order::whereNotIn('status', [OrderStatus::Dibatalkan])->sum('total'),

            // Pelanggan Aktif (role customer yang tidak di-soft-delete)
            'totalCustomersCount'   => User::where('role', 'customer')->count(),

            // Kemitraan Grosir
            'pendingPartnersCount'  => PartnerApplication::where('is_followed_up', false)->count(),

            // Produk & Stok Aktual
            'activeProductsCount'   => Product::where('is_active', true)->count(),
            'totalStockCount'       => (int) ProductVariant::where('is_active', true)->sum('stock_quantity'),
            'lowStockCount'         => ProductVariant::lowStock()->count(),

            // 5 Transaksi Pesanan Terkini
            'recentOrders'          => Order::with(['items', 'user'])->latest()->take(5)->get(),
        ]);
    }
}
