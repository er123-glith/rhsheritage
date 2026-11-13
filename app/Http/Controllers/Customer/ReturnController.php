<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Enums\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreReturnRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\ReturnEvidence;
use App\Models\ReturnItem;
use App\Models\ReturnRefund;
use App\Models\ReturnShipment;
use App\Models\ReturnStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar retur milik customer yang login.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $query = OrderReturn::with(['order', 'items.orderItem'])
            ->where('user_id', $userId)
            ->latest('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $returns = $query->paginate(10)->withQueryString();

        $totalCount = OrderReturn::where('user_id', $userId)->count();
        $activeCount = OrderReturn::where('user_id', $userId)
            ->whereNotIn('status', [ReturnStatus::Selesai->value, ReturnStatus::Ditolak->value])
            ->count();
        $completedCount = OrderReturn::where('user_id', $userId)
            ->where('status', ReturnStatus::Selesai->value)
            ->count();

        return view('customer.returns.index', compact('returns', 'totalCount', 'activeCount', 'completedCount'));
    }

    /**
     * Menampilkan form pengajuan retur untuk pesanan tertentu.
     */
    public function create(string $orderNumber): View|RedirectResponse
    {
        $order = Order::with(['orderItems.variant.product.primaryImage'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Otorisasi kepemilikan pesanan
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengajukan retur pada pesanan ini.');
        }

        // Syarat status pesanan: minimal sudah dikirim atau selesai
        if (! in_array($order->status, [OrderStatus::Dikirim, OrderStatus::Selesai], true)) {
            return redirect()->route('customer.orders.show', $order->order_number)
                ->with('error', 'Pengajuan retur hanya dapat dilakukan setelah barang dikirim atau diterima secara fisik.');
        }

        // Cek apakah ada retur aktif yang sedang diproses
        if ($order->hasActiveReturn()) {
            return redirect()->route('customer.orders.show', $order->order_number)
                ->with('error', 'Pesanan ini sudah memiliki pengajuan retur aktif yang sedang diproses.');
        }

        return view('customer.returns.create', compact('order'));
    }

    /**
     * Menyimpan formulir pengajuan retur baru.
     */
    public function store(StoreReturnRequest $request): RedirectResponse
    {
        $user = $request->user();
        $order = Order::findOrFail($request->input('order_id'));

        return DB::transaction(function () use ($request, $user, $order) {
            $returnNumber = 'RET-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $orderReturn = OrderReturn::create([
                'return_number' => $returnNumber,
                'order_id'      => $order->id,
                'user_id'       => $user->id,
                'solution'      => $request->input('solution'),
                'reason'        => $request->input('reason'),
                'description'   => $request->input('description'),
                'status'        => ReturnStatus::MenungguReview,
                'agreed_terms'  => true,
            ]);

            // Simpan rincian item produk yang diretur
            $items = $request->input('items', []);
            $totalRefundEstimate = 0;

            foreach ($items as $itemData) {
                if (empty($itemData['id']) || empty($itemData['qty'])) {
                    continue;
                }

                $orderItem = OrderItem::findOrFail($itemData['id']);
                $qty = (int) $itemData['qty'];
                $subtotal = $orderItem->unit_price * $qty;
                $totalRefundEstimate += $subtotal;

                ReturnItem::create([
                    'return_id'          => $orderReturn->id,
                    'order_item_id'      => $orderItem->id,
                    'product_variant_id' => $orderItem->product_variant_id,
                    'quantity'           => $qty,
                    'unit_price'         => $orderItem->unit_price,
                    'subtotal'           => $subtotal,
                ]);
            }

            // Simpan Video Unboxing Wajib
            if ($request->hasFile('unboxing_video')) {
                $video = $request->file('unboxing_video');
                $videoPath = $video->store('secure_proofs/returns/videos', 'local');

                ReturnEvidence::create([
                    'return_id'     => $orderReturn->id,
                    'file_type'     => 'unboxing_video',
                    'file_path'     => $videoPath,
                    'original_name' => $video->getClientOriginalName(),
                    'file_size'     => $video->getSize(),
                    'mime_type'     => $video->getMimeType(),
                ]);
            }

            // Simpan Foto Bukti Fisik Wajib
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $photoPath = $photo->store('secure_proofs/returns/photos', 'local');

                    ReturnEvidence::create([
                        'return_id'     => $orderReturn->id,
                        'file_type'     => 'photo',
                        'file_path'     => $photoPath,
                        'original_name' => $photo->getClientOriginalName(),
                        'file_size'     => $photo->getSize(),
                        'mime_type'     => $photo->getMimeType(),
                    ]);
                }
            }

            // Jika solusi adalah refund, buat entri awal return_refunds
            if ($orderReturn->solution === 'refund') {
                ReturnRefund::create([
                    'return_id' => $orderReturn->id,
                    'amount'    => $totalRefundEstimate,
                    'status'    => RefundStatus::BelumDiproses,
                ]);
            }

            // Catat log histori status
            ReturnStatusHistory::create([
                'return_id'   => $orderReturn->id,
                'from_status' => null,
                'to_status'   => ReturnStatus::MenungguReview->value,
                'actor_type'  => 'customer',
                'actor_id'    => $user->id,
                'note'        => 'Pengajuan retur diajukan oleh pelanggan lengkap dengan video unboxing dan foto bukti.',
                'created_at'  => now(),
            ]);

            return redirect()->route('customer.returns.show', $orderReturn->return_number)
                ->with('status', "Pengajuan retur #{$orderReturn->return_number} berhasil dikirim dan sedang menunggu peninjauan oleh tim RHS Heritage.");
        });
    }

    /**
     * Menampilkan detail retur tertentu milik customer.
     */
    public function show(string $returnNumber): View
    {
        $return = OrderReturn::with([
            'order.shippingZone',
            'items.orderItem.variant.product.primaryImage',
            'evidences',
            'shipments',
            'refund',
            'statusHistories.actor',
        ])
            ->where('return_number', $returnNumber)
            ->firstOrFail();

        if ($return->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pengajuan retur ini.');
        }

        return view('customer.returns.show', compact('return'));
    }

    /**
     * Customer mengunggah resi J&T pengiriman balik barang ke gudang.
     */
    public function shipReturn(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::where('return_number', $returnNumber)->firstOrFail();

        if ($return->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if (! in_array($return->status, [ReturnStatus::Disetujui, ReturnStatus::MenungguBarangBalik], true)) {
            return back()->with('error', 'Status retur tidak mengizinkan pengunggahan resi pengembalian saat ini.');
        }

        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:60'],
            'shipping_proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ], [
            'tracking_number.required' => 'Nomor resi J&T pengiriman balik wajib diisi.',
            'shipping_proof.required'  => 'Foto bukti struk resi J&T wajib diunggah.',
            'shipping_proof.mimes'     => 'Foto bukti resi harus berformat JPG, JPEG, PNG, atau WEBP.',
            'shipping_proof.max'       => 'Ukuran foto bukti resi maksimal 5 MB.',
        ]);

        $proofPath = $request->file('shipping_proof')->store('secure_proofs/returns/shipments', 'local');

        DB::transaction(function () use ($return, $validated, $proofPath) {
            ReturnShipment::create([
                'return_id'           => $return->id,
                'type'                => 'return_from_customer',
                'courier'             => 'J&T Express',
                'tracking_number'     => trim($validated['tracking_number']),
                'shipped_at'          => now(),
                'shipping_proof_path' => $proofPath,
                'notes'               => $validated['notes'] ?? null,
            ]);

            $return->update([
                'status' => ReturnStatus::BarangDiperiksa,
            ]);

            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => $return->getOriginal('status')?->value ?? $return->status->value,
                'to_status'   => ReturnStatus::BarangDiperiksa->value,
                'actor_type'  => 'customer',
                'actor_id'    => auth()->id(),
                'note'        => "Pelanggan telah mengirim barang via J&T Express (Resi: {$validated['tracking_number']}). Menunggu pemeriksaan di gudang.",
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', 'Bukti pengiriman balik berhasil disimpan. Tim kami akan memeriksa barang setelah sampai.');
    }

    /**
     * Customer mengonfirmasi bahwa barang pengganti telah diterima dengan baik.
     */
    public function confirmReplacement(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::where('return_number', $returnNumber)->firstOrFail();

        if ($return->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($return->solution !== 'replacement' || $return->status !== ReturnStatus::DalamPenyelesaian) {
            return back()->with('error', 'Penggantian belum dapat dikonfirmasi pada status saat ini.');
        }

        $request->validate([
            'agreement'      => ['accepted'],
            'received_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $proofPath = null;
        if ($request->hasFile('received_proof')) {
            $proofPath = $request->file('received_proof')->store('secure_proofs/returns/received', 'local');
        }

        DB::transaction(function () use ($return, $proofPath) {
            $replacementShipment = $return->replacementShipment();
            if ($replacementShipment) {
                $replacementShipment->update([
                    'received_at'         => now(),
                    'received_proof_path' => $proofPath,
                ]);
            }

            $return->update([
                'status' => ReturnStatus::Selesai,
            ]);

            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => ReturnStatus::DalamPenyelesaian->value,
                'to_status'   => ReturnStatus::Selesai->value,
                'actor_type'  => 'customer',
                'actor_id'    => auth()->id(),
                'note'        => 'Pelanggan telah menerima barang pengganti dan mengonfirmasi penyelesaian retur.',
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', 'Terima kasih! Penggantian barang telah selesai.');
    }

    /**
     * Customer mengonfirmasi bahwa pengembalian dana (refund) telah masuk ke rekening.
     */
    public function confirmRefund(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::where('return_number', $returnNumber)->firstOrFail();

        if ($return->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($return->solution !== 'refund' || ! in_array($return->status, [ReturnStatus::DalamPenyelesaian, ReturnStatus::Disetujui], true)) {
            return back()->with('error', 'Konfirmasi refund belum dapat dilakukan.');
        }

        $refund = $return->refund;
        if (! $refund || $refund->status !== RefundStatus::MenungguKonfirmasi) {
            return back()->with('error', 'Dana belum ditransfer atau belum dalam status menunggu konfirmasi.');
        }

        $request->validate([
            'agreement' => ['accepted'],
        ]);

        DB::transaction(function () use ($return, $refund) {
            $refund->update([
                'status'                => RefundStatus::Diterima,
                'confirmed_received_at' => now(),
            ]);

            $return->update([
                'status' => ReturnStatus::Selesai,
            ]);

            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => $return->status->value,
                'to_status'   => ReturnStatus::Selesai->value,
                'actor_type'  => 'customer',
                'actor_id'    => auth()->id(),
                'note'        => 'Pelanggan telah mengonfirmasi bahwa dana refund telah diterima dengan benar.',
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', 'Terima kasih! Pengembalian dana telah selesai.');
    }
}
