<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RefundStatus;
use App\Enums\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\ProductVariant;
use App\Models\ReturnRefund;
use App\Models\ReturnShipment;
use App\Models\ReturnStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengajuan retur pelanggan dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $query = OrderReturn::with(['order', 'user', 'items.orderItem'])
            ->latest('id');

        if ($status = $request->input('status')) {
            if ($status === 'dalam_penyelesaian') {
                $query->whereIn('status', [
                    ReturnStatus::Disetujui,
                    ReturnStatus::MenungguBarangBalik,
                    ReturnStatus::BarangDiperiksa,
                    ReturnStatus::DalamPenyelesaian,
                    ReturnStatus::PerluInformasi,
                ]);
            } else {
                $query->where('status', $status);
            }
        }

        if ($solution = $request->input('solution')) {
            $query->where('solution', $solution);
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($sub) use ($search) {
                      $sub->where('order_number', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        $returns = $query->paginate(15)->withQueryString();

        // Hitung statistik untuk badge tab navigasi
        $pendingReviewCount = OrderReturn::where('status', ReturnStatus::MenungguReview)->count();
        $inProgressCount    = OrderReturn::whereIn('status', [
            ReturnStatus::Disetujui,
            ReturnStatus::MenungguBarangBalik,
            ReturnStatus::BarangDiperiksa,
            ReturnStatus::DalamPenyelesaian,
            ReturnStatus::PerluInformasi,
        ])->count();
        $completedCount     = OrderReturn::where('status', ReturnStatus::Selesai)->count();
        $rejectedCount      = OrderReturn::where('status', ReturnStatus::Ditolak)->count();
        $totalCount         = OrderReturn::count();

        $counts = [
            'all'                => $totalCount,
            'menunggu_review'    => $pendingReviewCount,
            'dalam_penyelesaian' => $inProgressCount,
            'selesai'            => $completedCount,
            'ditolak'            => $rejectedCount,
        ];

        return view('admin.returns.index', compact(
            'returns',
            'counts',
            'pendingReviewCount',
            'inProgressCount',
            'completedCount',
            'rejectedCount',
            'totalCount'
        ));
    }

    /**
     * Menampilkan detail lengkap pengajuan retur (informasi produk, video unboxing, foto bukti, status).
     */
    public function show(string $returnNumber): View
    {
        $return = OrderReturn::with([
            'order.shippingZone',
            'user',
            'items.orderItem.variant.product.primaryImage',
            'evidences',
            'shipments',
            'refund',
            'statusHistories.actor',
        ])
            ->where('return_number', $returnNumber)
            ->firstOrFail();

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Memproses tinjauan awal pengajuan retur (Setujui / Tolak / Minta Informasi).
     */
    public function review(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::where('return_number', $returnNumber)->firstOrFail();

        $action = $request->input('action'); // 'approve', 'reject', 'need_info'

        if (! in_array($action, ['approve', 'reject', 'need_info'], true)) {
            return back()->with('error', 'Tindakan peninjauan tidak valid.');
        }

        if ($action === 'reject') {
            $validated = $request->validate([
                'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
            ], [
                'rejection_reason.required' => 'Alasan penolakan pengajuan retur wajib diisi.',
            ]);

            DB::transaction(function () use ($return, $validated) {
                $return->update([
                    'status'           => ReturnStatus::Ditolak,
                    'rejection_reason' => $validated['rejection_reason'],
                ]);

                ReturnStatusHistory::create([
                    'return_id'   => $return->id,
                    'from_status' => $return->getOriginal('status')?->value ?? $return->status->value,
                    'to_status'   => ReturnStatus::Ditolak->value,
                    'actor_type'  => 'admin',
                    'actor_id'    => auth()->id(),
                    'note'        => "Pengajuan retur ditolak oleh Admin. Alasan: {$validated['rejection_reason']}",
                    'created_at'  => now(),
                ]);
            });

            return back()->with('status', 'Pengajuan retur telah ditolak.');
        }

        if ($action === 'need_info') {
            $validated = $request->validate([
                'admin_notes' => ['required', 'string', 'min:5', 'max:1000'],
            ], [
                'admin_notes.required' => 'Catatan informasi yang dibutuhkan wajib diisi.',
            ]);

            DB::transaction(function () use ($return, $validated) {
                $return->update([
                    'status'      => ReturnStatus::PerluInformasi,
                    'admin_notes' => $validated['admin_notes'],
                ]);

                ReturnStatusHistory::create([
                    'return_id'   => $return->id,
                    'from_status' => $return->getOriginal('status')?->value ?? $return->status->value,
                    'to_status'   => ReturnStatus::PerluInformasi->value,
                    'actor_type'  => 'admin',
                    'actor_id'    => auth()->id(),
                    'note'        => "Admin meminta informasi tambahan: {$validated['admin_notes']}",
                    'created_at'  => now(),
                ]);
            });

            return back()->with('status', 'Status diperbarui. Pelanggan telah diminta memberikan informasi tambahan.');
        }

        if ($action === 'approve') {
            $validated = $request->validate([
                'admin_notes' => ['nullable', 'string', 'max:1000'],
            ]);

            DB::transaction(function () use ($return, $validated) {
                // Untuk replacement atau refund barang fisik, alur dilanjutkan ke menunggu barang balik
                $nextStatus = ReturnStatus::MenungguBarangBalik;

                $return->update([
                    'status'      => $nextStatus,
                    'admin_notes' => $validated['admin_notes'] ?? 'Pengajuan retur disetujui. Silakan kirim barang ke alamat gudang RHS Heritage via J&T Express.',
                ]);

                ReturnStatusHistory::create([
                    'return_id'   => $return->id,
                    'from_status' => $return->getOriginal('status')?->value ?? $return->status->value,
                    'to_status'   => $nextStatus->value,
                    'actor_type'  => 'admin',
                    'actor_id'    => auth()->id(),
                    'note'        => 'Pengajuan retur disetujui oleh Admin. Menunggu pengiriman barang balik dari pelanggan.',
                    'created_at'  => now(),
                ]);
            });

            return back()->with('status', 'Pengajuan retur berhasil disetujui.');
        }

        return back();
    }

    /**
     * Admin memverifikasi bahwa barang retur fisik telah tiba di gudang dan telah diperiksa.
     */
    public function verifyReceived(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::where('return_number', $returnNumber)->firstOrFail();

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($return, $validated) {
            $customerShipment = $return->customerShipment();
            if ($customerShipment) {
                $customerShipment->update([
                    'received_at' => now(),
                    'notes'       => $validated['notes'] ?? $customerShipment->notes,
                ]);
            }

            $return->update([
                'status' => ReturnStatus::BarangDiperiksa,
            ]);

            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => $return->status->value,
                'to_status'   => ReturnStatus::BarangDiperiksa->value,
                'actor_type'  => 'admin',
                'actor_id'    => auth()->id(),
                'note'        => 'Barang retur telah diterima di gudang dan diverifikasi kondisinya oleh Admin.',
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', 'Penerimaan barang retur berhasil diverifikasi. Anda sekarang dapat memproses barang pengganti atau pengembalian dana.');
    }

    /**
     * Admin memproses pengiriman barang pengganti (Replacement) dengan validasi stok & upload resi J&T.
     */
    public function processReplacement(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::with('items')->where('return_number', $returnNumber)->firstOrFail();

        if ($return->solution !== 'replacement') {
            return back()->with('error', 'Pengajuan ini bukan untuk penggantian barang.');
        }

        $validated = $request->validate([
            'tracking_number' => ['required', 'string', 'max:60'],
            'shipping_date'   => ['required', 'date'],
            'shipping_proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ], [
            'tracking_number.required' => 'Nomor resi J&T barang pengganti wajib diisi.',
            'shipping_date.required'   => 'Tanggal pengiriman barang pengganti wajib diisi.',
            'shipping_proof.required'  => 'Foto bukti pengiriman J&T wajib diunggah.',
            'shipping_proof.max'       => 'Ukuran foto bukti resi pengiriman maksimal 2 MB.',
        ]);

        return DB::transaction(function () use ($return, $request, $validated) {
            // 1. Validasi dan potong stok varian barang pengganti
            foreach ($return->items as $returnItem) {
                if ($returnItem->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($returnItem->product_variant_id);
                    if ($variant) {
                        if ($variant->stock_quantity < $returnItem->quantity) {
                            return back()->with('error', "Stok barang pengganti untuk '{$variant->product->name} - {$variant->sku}' tidak mencukupi (Tersedia: {$variant->stock_quantity}, Dibutuhkan: {$returnItem->quantity}).");
                        }
                        $variant->decrement('stock_quantity', $returnItem->quantity);
                    }
                }
            }

            // 2. Simpan bukti resi ke private storage
            $proofPath = $request->file('shipping_proof')->store('secure_proofs/returns/replacements', 'local');

            // 3. Simpan pengiriman barang pengganti
            ReturnShipment::create([
                'return_id'           => $return->id,
                'type'                => 'replacement_from_admin',
                'courier'             => 'J&T Express',
                'tracking_number'     => trim($validated['tracking_number']),
                'shipped_at'          => $validated['shipping_date'],
                'shipping_proof_path' => $proofPath,
                'notes'               => $validated['notes'] ?? null,
            ]);

            // 4. Ubah status retur menjadi DalamPenyelesaian
            $return->update([
                'status' => ReturnStatus::DalamPenyelesaian,
            ]);

            // 5. Catat log riwayat
            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => $return->status->value,
                'to_status'   => ReturnStatus::DalamPenyelesaian->value,
                'actor_type'  => 'admin',
                'actor_id'    => auth()->id(),
                'note'        => "Admin telah mengirim barang pengganti via J&T Express (Resi: {$validated['tracking_number']}). Stok pengganti telah dipotong.",
                'created_at'  => now(),
            ]);

            return back()->with('status', 'Barang pengganti berhasil diproses dan dikirim via J&T Express.');
        });
    }

    /**
     * Admin memproses pengembalian dana (Refund) dengan pencatatan bukti transfer nyata.
     */
    public function processRefund(Request $request, string $returnNumber): RedirectResponse
    {
        $return = OrderReturn::with('items')->where('return_number', $returnNumber)->firstOrFail();

        if ($return->solution !== 'refund') {
            return back()->with('error', 'Pengajuan ini bukan untuk pengembalian dana.');
        }

        // Hitung batas maksimal pengembalian dana berdasarkan item asli
        $maxRefund = (float) $return->items->sum('subtotal');

        $validated = $request->validate([
            'amount'            => ['required', 'numeric', 'min:1', "max:{$maxRefund}"],
            'bank_name'         => ['required', 'string', 'max:60'],
            'account_number'    => ['required', 'string', 'max:60'],
            'account_name'      => ['required', 'string', 'max:100'],
            'payment_reference' => ['required', 'string', 'max:100'],
            'proof'             => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ], [
            'amount.max'                => "Nominal refund tidak boleh melebihi nilai sah produk yang diretur (Rp" . number_format($maxRefund, 0, ',', '.') . ").",
            'payment_reference.required' => 'Nomor referensi mutasi transfer wajib diisi.',
            'proof.required'            => 'Bukti transfer pengembalian dana wajib diunggah.',
        ]);

        $proofPath = $request->file('proof')->store('secure_proofs/returns/refunds', 'local');

        DB::transaction(function () use ($return, $validated, $proofPath) {
            ReturnRefund::updateOrCreate(
                ['return_id' => $return->id],
                [
                    'amount'            => $validated['amount'],
                    'bank_name'         => $validated['bank_name'],
                    'account_number'    => $validated['account_number'],
                    'account_name'      => $validated['account_name'],
                    'status'            => RefundStatus::MenungguKonfirmasi,
                    'payment_reference' => $validated['payment_reference'],
                    'proof_path'        => $proofPath,
                    'transferred_at'    => now(),
                    'notes'             => $validated['notes'] ?? null,
                ]
            );

            $return->update([
                'status' => ReturnStatus::DalamPenyelesaian,
            ]);

            ReturnStatusHistory::create([
                'return_id'   => $return->id,
                'from_status' => $return->status->value,
                'to_status'   => ReturnStatus::DalamPenyelesaian->value,
                'actor_type'  => 'admin',
                'actor_id'    => auth()->id(),
                'note'        => "Admin telah mentransfer dana refund sebesar Rp" . number_format($validated['amount'], 0, ',', '.') . " (Ref: {$validated['payment_reference']}). Menunggu konfirmasi penerimaan oleh pelanggan.",
                'created_at'  => now(),
            ]);
        });

        return back()->with('status', 'Bukti transfer pengembalian dana berhasil dicatat. Menunggu konfirmasi penerimaan oleh pelanggan.');
    }
}

