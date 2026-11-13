<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppLinkGenerator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Menampilkan riwayat pesanan milik customer yang sedang login.
     * Mendukung filter status, pencarian nomor pesanan, dan pagination.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();

        // Hitung statistik pesanan customer per status untuk tab filter
        $totalOrders = Order::where('user_id', $userId)->count();
        $pendingCount = Order::where('user_id', $userId)->where('status', OrderStatus::MenungguKonfirmasi)->count();
        $processingCount = Order::where('user_id', $userId)->where('status', OrderStatus::Diproses)->count();
        $shippedCount = Order::where('user_id', $userId)->where('status', OrderStatus::Dikirim)->count();
        $completedCount = Order::where('user_id', $userId)->where('status', OrderStatus::Selesai)->count();
        $cancelledCount = Order::where('user_id', $userId)->where('status', OrderStatus::Dibatalkan)->count();

        $query = Order::with(['orderItems', 'shippingZone'])
            ->where('user_id', $userId)
            ->latest();

        // Filter status pesanan
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter pencarian berdasarkan nomor pesanan
        if ($search = $request->input('q')) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.index', compact(
            'orders',
            'totalOrders',
            'pendingCount',
            'processingCount',
            'shippedCount',
            'completedCount',
            'cancelledCount'
        ));
    }

    /**
     * Menampilkan detail pesanan spesifik milik customer.
     * Keamanan: hanya customer pemilik pesanan yang diizinkan mengakses (SEC-05).
     */
    public function show(string $orderNumber, WhatsAppLinkGenerator $wa): View
    {
        $order = Order::with(['orderItems.variant.product.primaryImage', 'shippingZone', 'statusLogs'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi: Customer hanya boleh melihat pesanannya sendiri.
        if ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pesanan ini.');
        }

        $whatsappUrl = $wa->forOrder($order);

        return view('customer.orders.show', compact('order', 'whatsappUrl'));
    }
}

