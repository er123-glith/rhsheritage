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

        $query = Order::with(['orderItems', 'shippingZone', 'review'])
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
        $order = Order::with(['orderItems.variant.product.primaryImage', 'shippingZone', 'statusLogs', 'review', 'returns', 'deliveryConfirmedBy'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi: Customer hanya boleh melihat pesanannya sendiri.
        if ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pesanan ini.');
        }

        $whatsappUrl = $wa->forOrder($order);

        return view('customer.orders.show', compact('order', 'whatsappUrl'));
    }

    /**
     * Tampilkan dokumen invoice resmi pesanan customer berstandar A4 siap cetak dan ekspor ke PDF.
     */
    public function invoice(string $orderNumber): View
    {
        $order = Order::with(['orderItems', 'shippingZone', 'user', 'deliveryConfirmedBy'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi: Customer hanya boleh melihat invoice pesanannya sendiri.
        if ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat invoice pesanan ini.');
        }

        return view('invoices.order-invoice', compact('order'));
    }

    /**
     * Konfirmasi penerimaan barang oleh customer dengan foto bukti dan persetujuan.
     */
    public function confirmDelivery(Request $request, string $orderNumber): \Illuminate\Http\RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Otorisasi: Hanya customer pemilik pesanan yang sah
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengonfirmasi pesanan ini.');
        }

        // Syarat: Pesanan harus dalam status 'Dikirim'
        if ($order->status !== OrderStatus::Dikirim) {
            return back()->with('error', "Pesanan tidak dapat dikonfirmasi pada status saat ini ({$order->status->label()}).");
        }

        // Syarat: Tidak boleh ada sengketa atau retur aktif yang belum tuntas
        if ($order->hasActiveReturn()) {
            return back()->with('error', 'Pesanan memiliki pengajuan retur aktif. Konfirmasi selesai tidak dapat dilakukan hingga retur terselesaikan.');
        }

        $maxKb = config('uploads.delivery_proof.max_kb', 5120);
        $mimesRule = config('uploads.delivery_proof.mimes_rule', 'mimes:jpg,jpeg,png,webp');
        $uploadErrors = config('uploads.delivery_proof.error_messages', []);

        $validated = $request->validate([
            'agreement'      => ['accepted'],
            'delivery_proof' => ['required', 'file', $mimesRule, 'max:' . $maxKb],
        ], array_merge([
            'agreement.accepted' => 'Anda wajib menyetujui pernyataan bahwa barang pesanan telah diterima dengan baik.',
        ], $uploadErrors));

        $proofPath = $request->file('delivery_proof')->store('secure_proofs/delivery', 'local');

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $proofPath) {
            $order->update([
                'status'                => OrderStatus::Selesai,
                'delivered_at'          => now(),
                'delivery_proof_path'   => $proofPath,
                'delivery_confirmed_by' => auth()->id(),
            ]);

            $order->statusLogs()->create([
                'from_status' => OrderStatus::Dikirim->value,
                'to_status'   => OrderStatus::Selesai->value,
                'note'        => 'Pembeli telah mengonfirmasi penerimaan barang dan mengunggah foto bukti penerimaan.',
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', "Pesanan #{$order->order_number} telah dikonfirmasi selesai. Terima kasih telah berbelanja di RHS Heritage!");
    }
}

