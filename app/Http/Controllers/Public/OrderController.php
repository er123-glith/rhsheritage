<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackOrderRequest;
use App\Models\Order;
use App\Services\WhatsAppLinkGenerator;

class OrderController extends Controller
{
    /**
     * Halaman konfirmasi pesanan setelah checkout berhasil.
     * Route model binding menggunakan kolom order_number.
     */
    public function confirmation(string $orderNumber, WhatsAppLinkGenerator $wa)
    {
        $order = Order::with(['orderItems', 'shippingZone'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi akses: customer tidak boleh melihat pesanan customer lain (SEC-05, Section G & H)
        if ($order->user_id !== null) {
            $user = auth()->user();
            if (! $user || ($user->id !== $order->user_id && ! $user->isAdmin())) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pesanan ini.');
            }
        }

        $whatsappUrl = $wa->forOrder($order);

        return view('public.order.confirmation', compact('order', 'whatsappUrl'));
    }

    /**
     * Tampilkan invoice resmi untuk konfirmasi pesanan publik.
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::with(['orderItems', 'shippingZone', 'user', 'deliveryConfirmedBy'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi akses jika order terikat dengan user akun
        if ($order->user_id !== null) {
            $user = auth()->user();
            if (! $user || ($user->id !== $order->user_id && ! $user->isAdmin())) {
                if (! $user) {
                    return redirect()->route('login')->with('info', 'Silakan login terlebih dahulu untuk mengakses invoice pesanan Anda.');
                }
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat invoice ini.');
            }
        }

        return view('invoices.order-invoice', compact('order'));
    }

    /** Tampilkan riwayat pesanan milik customer yang sedang login. */
    public function myOrders()
    {
        $orders = Order::with(['orderItems', 'shippingZone'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('public.order.my-orders', compact('orders'));
    }

    /** Tampilkan form pelacakan pesanan. */
    public function trackForm()
    {
        return view('public.order.track');
    }

    /**
     * Proses pencarian status pesanan.
     * SEC-04: Wajib dua kunci verifikasi — nomor pesanan DAN nomor HP.
     */
    public function track(TrackOrderRequest $request)
    {
        $order = Order::with(['orderItems', 'shippingZone', 'statusLogs', 'returns', 'deliveryConfirmedBy'])
            ->where('order_number', strtoupper(trim($request->validated('order_number'))))
            ->where('customer_phone', $request->validated('customer_phone'))
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->with('error', 'Pesanan tidak ditemukan. Pastikan nomor pesanan dan nomor HP yang Anda masukkan sudah benar.');
        }

        return view('public.order.track', compact('order'));
    }
}
