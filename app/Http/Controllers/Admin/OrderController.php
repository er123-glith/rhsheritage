<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockOnConfirmException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        // Hitung statistik pesanan per status
        $pendingCount = Order::where('status', OrderStatus::MenungguKonfirmasi)->count();
        $processingCount = Order::where('status', OrderStatus::Diproses)->count();
        $shippedCount = Order::where('status', OrderStatus::Dikirim)->count();
        $completedCount = Order::where('status', OrderStatus::Selesai)->count();
        $cancelledCount = Order::where('status', OrderStatus::Dibatalkan)->count();
        $totalOrders = Order::count();

        $query = Order::with(['items', 'shippingZone'])->orderBy('id', 'desc');

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter pencarian
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter rentang tanggal
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact(
            'orders',
            'pendingCount',
            'processingCount',
            'shippedCount',
            'completedCount',
            'cancelledCount',
            'totalOrders'
        ));
    }

    public function show(Order $order): View
    {
        $order->load(['items.variant.product.primaryImage', 'shippingZone', 'statusLogs']);

        // Sanitasi nomor telepon pelanggan untuk link WhatsApp (e.g. 0812 -> 62812)
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $waMessage = rawurlencode("Halo Kak {$order->customer_name}, kami dari RHS HERITAGE ingin mengonfirmasi pesanan #{$order->order_number}.");
        $customerWaUrl = "https://wa.me/{$phone}?text={$waMessage}";

        return view('admin.orders.show', compact('order', 'customerWaUrl'));
    }

    public function confirm(Order $order, OrderStatusService $service): RedirectResponse
    {
        try {
            $service->confirm($order);
        } catch (InsufficientStockOnConfirmException $e) {
            return back()->with('error', $e->getMessage());
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', "Pesanan #{$order->order_number} berhasil dikonfirmasi. Status diubah menjadi 'Diproses' dan stok telah dipotong.");
    }

    public function cancel(Request $request, Order $order, OrderStatusService $service): RedirectResponse
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $service->cancel($order, $validated['cancellation_reason']);
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', "Pesanan #{$order->order_number} berhasil dibatalkan. Alasan: \"{$validated['cancellation_reason']}\".");
    }

    public function updateStatus(Request $request, Order $order, OrderStatusService $service): RedirectResponse
    {
        $target = $request->input('target_status');

        try {
            if ($target === OrderStatus::Dikirim->value) {
                $validated = $request->validate([
                    'tracking_number' => ['nullable', 'string', 'max:100'],
                ]);
                $service->markShipped($order, $validated['tracking_number'] ?? null);
                return back()->with('status', "Pesanan #{$order->order_number} berhasil ditandai sebagai 'Dikirim'.");
            } elseif ($target === OrderStatus::Selesai->value) {
                $service->markCompleted($order);
                return back()->with('status', "Pesanan #{$order->order_number} berhasil diselesaikan.");
            } else {
                return back()->with('error', 'Target status tidak valid.');
            }
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
