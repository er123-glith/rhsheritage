<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard customer dengan statistik aktual dan pesanan terbaru.
     */
    public function index(): View
    {
        $userId = auth()->id();

        // Data statistik aktual milik customer yang sedang login (tidak menggunakan dummy data)
        $totalOrders = Order::where('user_id', $userId)->count();
        $pendingOrders = Order::where('user_id', $userId)->where('status', OrderStatus::MenungguKonfirmasi)->count();
        $processingOrders = Order::where('user_id', $userId)->whereIn('status', [OrderStatus::Diproses, OrderStatus::Dikirim])->count();
        $completedOrders = Order::where('user_id', $userId)->where('status', OrderStatus::Selesai)->count();

        // 5 pesanan terbaru
        $recentOrders = Order::with(['orderItems', 'shippingZone'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'recentOrders'
        ));
    }
}

