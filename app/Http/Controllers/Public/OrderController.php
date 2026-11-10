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

        $whatsappUrl = $wa->forOrder($order);

        return view('public.order.confirmation', compact('order', 'whatsappUrl'));
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
        $order = Order::with(['orderItems', 'shippingZone', 'statusLogs'])
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
