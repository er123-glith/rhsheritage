<x-layouts.admin>
    <x-slot:title>Detail Retur {{ $return->return_number }}</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('admin.returns.index') }}" class="hover:text-primary-700 transition">Manajemen Retur</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium font-mono">{{ $return->return_number }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700 font-mono">
                        {{ $return->return_number }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $return->status->badgeClass() }}">
                        {{ $return->status->label() }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $return->solution === 'replacement' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $return->solution === 'replacement' ? 'Penggantian Barang' : 'Pengembalian Dana' }}
                    </span>
                </div>
                <p class="font-body text-xs text-neutral-400 mt-1">
                    Diajukan pada {{ $return->created_at->format('d F Y, H:i') }} WIB &bull; Terkait Pesanan <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-primary-700 font-semibold hover:underline font-mono">{{ $return->order->order_number }}</a>
                </p>
            </div>

            <!-- Action Buttons based on status -->
            <div class="flex flex-wrap items-center gap-2">
                @if(in_array($return->status->value, ['menunggu_review', 'perlu_informasi']))
                    <button type="button" 
                            onclick="document.getElementById('modal-review').classList.remove('hidden')"
                            class="px-4 py-2 rounded-lg font-body text-sm font-semibold bg-gold-600 hover:bg-gold-700 text-white shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tinjau Pengajuan</span>
                    </button>
                @endif

                @if($return->status->value === 'barang_dikirim_customer')
                    <button type="button" 
                            onclick="document.getElementById('modal-verify-received').classList.remove('hidden')"
                            class="px-4 py-2 rounded-lg font-body text-sm font-semibold bg-primary-700 hover:bg-primary-800 text-white shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Konfirmasi Terima Barang Retur</span>
                    </button>
                @endif

                @if(in_array($return->status->value, ['barang_diperiksa', 'disetujui']))
                    @if($return->solution === 'replacement')
                        <button type="button" 
                                onclick="document.getElementById('modal-process-replacement').classList.remove('hidden')"
                                class="px-4 py-2 rounded-lg font-body text-sm font-semibold bg-primary-700 hover:bg-primary-800 text-white shadow-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Kirim Barang Pengganti J&T</span>
                        </button>
                    @elseif($return->solution === 'refund')
                        <button type="button" 
                                onclick="document.getElementById('modal-process-refund').classList.remove('hidden')"
                                class="px-4 py-2 rounded-lg font-body text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Proses Transfer Refund</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 font-body text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-body text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Admin Prompt / Guidance Banner -->
        @if($return->status->value === 'menunggu_review')
            <div class="p-4 rounded-xl bg-gold-50 border border-gold-300 text-gold-900 font-body text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-gold-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Pengajuan Menunggu Peninjauan Admin</p>
                    <p class="text-xs text-gold-800">
                        Periksa kelengkapan video unboxing dan foto bukti kerusakan di bawah ini secara teliti. Anda dapat menyetujui pengajuan, menolak dengan alasan tertulis, atau meminta informasi tambahan kepada pembeli.
                    </p>
                </div>
            </div>
        @elseif($return->status->value === 'barang_dikirim_customer')
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 font-body text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Pelanggan Telah Mengirimkan Barang Retur</p>
                    <p class="text-xs text-blue-800">
                        Nomor resi J&T: <span class="font-mono font-bold">{{ $return->customerShipment()?->tracking_number }}</span>. Setelah paket tiba di gudang dan fisik produk selesai diperiksa, silakan klik tombol <strong>Konfirmasi Terima Barang Retur</strong> di atas.
                    </p>
                </div>
            </div>
        @elseif($return->status->value === 'barang_diperiksa')
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-purple-900 font-body text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-purple-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Barang Retur Telah Tiba di Gudang</p>
                    <p class="text-xs text-purple-800">
                        @if($return->solution === 'replacement')
                            Kondisi fisik barang telah diverifikasi. Silakan proses pengiriman barang pengganti dengan menekan tombol <strong>Kirim Barang Pengganti J&T</strong>. Sistem akan memotong stok varian secara otomatis.
                        @else
                            Kondisi fisik barang telah diverifikasi. Silakan lakukan proses transfer pengembalian dana melalui tombol <strong>Proses Transfer Refund</strong>.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Main Info, Items, Evidences, Resolution Log -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Detail Pengajuan Retur -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Informasi Klaim Retur</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-body">
                        <div>
                            <span class="text-neutral-400 text-xs block">Alasan Retur:</span>
                            <span class="font-semibold text-neutral-800">{{ $return->reason }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 text-xs block">Pilihan Solusi:</span>
                            <span class="font-semibold text-neutral-800">
                                {{ $return->solution === 'replacement' ? 'Penggantian Barang Baru (Replacement)' : 'Pengembalian Dana (Refund)' }}
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-neutral-400 text-xs block">Keterangan & Kronologi Masalah:</span>
                            <div class="mt-1 p-3 rounded-lg bg-neutral-50 text-neutral-700 text-xs leading-relaxed border border-neutral-100">
                                {{ $return->description }}
                            </div>
                        </div>
                        @if($return->admin_notes)
                            <div class="sm:col-span-2">
                                <span class="text-gold-700 text-xs font-bold block">Catatan / Arahan Admin untuk Pelanggan:</span>
                                <div class="mt-1 p-3 rounded-lg bg-gold-50/70 text-gold-900 text-xs leading-relaxed border border-gold-200">
                                    {{ $return->admin_notes }}
                                </div>
                            </div>
                        @endif
                        @if($return->rejection_reason)
                            <div class="sm:col-span-2">
                                <span class="text-red-700 text-xs font-bold block">Alasan Penolakan Retur:</span>
                                <div class="mt-1 p-3 rounded-lg bg-red-50 text-red-900 text-xs leading-relaxed border border-red-200">
                                    {{ $return->rejection_reason }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Bukti Video Unboxing & Foto Kerusakan -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>Bukti Otentik Pelanggan</span>
                        </h2>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-primary-50 text-primary-700">
                            Wajib Video Unboxing
                        </span>
                    </div>

                    <!-- Video Unboxing Section -->
                    @php
                        $unboxingVideo = $return->unboxingVideo();
                    @endphp

                    @if($unboxingVideo)
                        <div class="space-y-2 p-4 rounded-xl bg-neutral-900 text-white">
                            <div class="flex items-center justify-between text-xs text-neutral-300">
                                <span class="flex items-center gap-1.5 font-semibold text-gold-400">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Rekaman Video Unboxing Paket Utuh
                                </span>
                                <span>{{ $unboxingVideo->file_name ?? 'unboxing-video' }} ({{ $unboxingVideo->file_size ? number_format($unboxingVideo->file_size / (1024 * 1024), 2) . ' MB' : '' }})</span>
                            </div>

                            <video controls preload="metadata" class="w-full max-h-96 rounded-lg bg-black">
                                <source src="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $unboxingVideo->id, 'filename' => basename($unboxingVideo->file_path)]) }}" type="{{ $unboxingVideo->mime_type ?? 'video/mp4' }}">
                                Browser Anda tidak mendukung pemutaran video HTML5 langsung.
                            </video>

                            <div class="text-right">
                                <a href="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $unboxingVideo->id, 'filename' => basename($unboxingVideo->file_path)]) }}" target="_blank" class="text-xs text-gold-400 hover:text-gold-300 underline font-body">
                                    Buka video di tab baru / Unduh
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-body flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Peringatan: Video unboxing tidak ditemukan pada berkas lampiran klaim ini.</span>
                        </div>
                    @endif

                    <!-- Photo Evidences Grid -->
                    @php
                        $photoEvidences = $return->photos();
                    @endphp

                    @if($photoEvidences->isNotEmpty())
                        <div class="space-y-2 pt-2">
                            <h3 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-500">
                                Foto Bukti Kerusakan / Cacat Produk ({{ $photoEvidences->count() }} foto):
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($photoEvidences as $photo)
                                    <a href="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $photo->id, 'filename' => basename($photo->file_path)]) }}" target="_blank" class="group relative aspect-square rounded-lg overflow-hidden border border-neutral-200 bg-neutral-100 hover:shadow-md transition">
                                        <img src="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $photo->id, 'filename' => basename($photo->file_path)]) }}" alt="Bukti Foto" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                                            Perbesar
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 3. Daftar Produk yang Diretur -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Produk yang Diajukan Retur</span>
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-body text-sm divide-y divide-neutral-200">
                            <thead class="bg-neutral-50 text-xs font-semibold uppercase tracking-wider text-neutral-500">
                                <tr>
                                    <th class="px-4 py-3">Produk & Varian</th>
                                    <th class="px-4 py-3 text-right">Harga Beli</th>
                                    <th class="px-4 py-3 text-center">Qty Pesanan</th>
                                    <th class="px-4 py-3 text-center">Qty Retur</th>
                                    <th class="px-4 py-3 text-right">Subtotal Retur</th>
                                    @if($return->solution === 'replacement')
                                        <th class="px-4 py-3 text-center">Stok Gudang</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach($return->items as $item)
                                    @php
                                        $variant = $item->productVariant;
                                    @endphp
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-neutral-800">
                                                {{ $item->orderItem->product_name ?? 'Produk' }}
                                            </div>
                                            <div class="text-xs text-neutral-500">
                                                Varian: {{ $item->orderItem->variant_label ?? '-' }} &bull; SKU: <span class="font-mono">{{ $variant?->sku ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap text-neutral-600">
                                            Rp{{ number_format((float) $item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap text-neutral-600">
                                            {{ $item->orderItem->quantity ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap font-bold text-primary-700">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap font-semibold text-neutral-800">
                                            Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                        </td>
                                        @if($return->solution === 'replacement')
                                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                                @if($variant)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $variant->stock_quantity >= $item->quantity ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $variant->stock_quantity }} unit
                                                    </span>
                                                @else
                                                    <span class="text-xs text-neutral-400">N/A</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-neutral-50/70 border-t border-neutral-200">
                                <tr>
                                    <td colspan="{{ $return->solution === 'replacement' ? 4 : 3 }}" class="px-4 py-3 text-right font-semibold text-neutral-700 text-xs uppercase tracking-wider">
                                        Total Nilai Klaim Retur:
                                    </td>
                                    <td class="px-4 py-3 text-right font-display text-base font-bold text-primary-700">
                                        Rp{{ number_format((float) $return->items->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                    @if($return->solution === 'replacement')
                                        <td></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- 4. Logistik & Penyelesaian (Replacement / Refund) -->
                @if($return->solution === 'replacement')
                    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                        <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Proses Logistik Penggantian Barang (J&T Express)</span>
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pengiriman Balik dari Customer -->
                            @php
                                $custShip = $return->customerShipment();
                            @endphp
                            <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/50 space-y-3">
                                <div class="flex items-center justify-between border-b border-neutral-200 pb-2">
                                    <h3 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-600">
                                        1. Pengiriman Balik Pelanggan
                                    </h3>
                                    @if($custShip)
                                        <span class="px-2 py-0.5 rounded text-2xs font-bold {{ $custShip->received_at ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $custShip->received_at ? 'Tiba di Gudang' : 'Dalam Pengiriman' }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-2xs font-bold bg-neutral-200 text-neutral-600">
                                            Belum Dikirim
                                        </span>
                                    @endif
                                </div>

                                @if($custShip)
                                    <div class="text-xs space-y-1.5 font-body">
                                        <div><span class="text-neutral-400">Ekspedisi:</span> <span class="font-semibold text-neutral-800">{{ $custShip->courier }}</span></div>
                                        <div><span class="text-neutral-400">No. Resi J&T:</span> <span class="font-mono font-bold text-primary-700">{{ $custShip->tracking_number }}</span></div>
                                        <div><span class="text-neutral-400">Tanggal Kirim:</span> <span class="text-neutral-700">{{ $custShip->shipped_at?->format('d M Y') ?? '-' }}</span></div>
                                        @if($custShip->received_at)
                                            <div><span class="text-neutral-400">Tiba di Gudang:</span> <span class="font-semibold text-green-700">{{ $custShip->received_at->format('d M Y, H:i') }} WIB</span></div>
                                        @endif
                                        @if($custShip->shipping_proof_path)
                                            <div class="pt-1">
                                                <a href="{{ route('secure.proof', ['type' => 'return_shipment', 'id' => $custShip->id, 'filename' => basename($custShip->shipping_proof_path)]) }}" target="_blank" class="inline-flex items-center gap-1 text-primary-700 hover:underline font-semibold">
                                                    Lihat Foto Resi Fisik Pelanggan &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-neutral-400 font-body italic">
                                        Pelanggan belum mengunggah nomor resi dan bukti pengiriman fisik ke gudang.
                                    </p>
                                @endif
                            </div>

                            <!-- Pengiriman Barang Pengganti dari Admin -->
                            @php
                                $adminShip = $return->adminReplacementShipment() ?? $return->replacementShipment();
                            @endphp
                            <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/50 space-y-3">
                                <div class="flex items-center justify-between border-b border-neutral-200 pb-2">
                                    <h3 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-600">
                                        2. Kirim Barang Pengganti (Admin)
                                    </h3>
                                    @if($adminShip)
                                        <span class="px-2 py-0.5 rounded text-2xs font-bold {{ $adminShip->received_at ? 'bg-green-100 text-green-800' : 'bg-primary-100 text-primary-800' }}">
                                            {{ $adminShip->received_at ? 'Diterima Customer' : 'Barang Dikirim' }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-2xs font-bold bg-neutral-200 text-neutral-600">
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </div>

                                @if($adminShip)
                                    <div class="text-xs space-y-1.5 font-body">
                                        <div><span class="text-neutral-400">Ekspedisi:</span> <span class="font-semibold text-neutral-800">{{ $adminShip->courier }}</span></div>
                                        <div><span class="text-neutral-400">Resi Pengganti:</span> <span class="font-mono font-bold text-primary-700">{{ $adminShip->tracking_number }}</span></div>
                                        <div><span class="text-neutral-400">Tanggal Kirim:</span> <span class="text-neutral-700">{{ $adminShip->shipped_at?->format('d M Y') ?? '-' }}</span></div>
                                        @if($adminShip->received_at)
                                            <div><span class="text-neutral-400">Konfirmasi Pelanggan:</span> <span class="font-semibold text-green-700">{{ $adminShip->received_at->format('d M Y, H:i') }} WIB</span></div>
                                        @endif
                                        @if($adminShip->shipping_proof_path)
                                            <div class="pt-1">
                                                <a href="{{ route('secure.proof', ['type' => 'return_shipment', 'id' => $adminShip->id, 'filename' => basename($adminShip->shipping_proof_path)]) }}" target="_blank" class="inline-flex items-center gap-1 text-primary-700 hover:underline font-semibold">
                                                    Lihat Foto Resi Pengganti Admin &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-xs text-neutral-400 font-body italic">
                                        Barang pengganti belum dikirim. Pastikan barang retur telah tiba di gudang sebelum mengirim barang baru.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($return->solution === 'refund')
                    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                        <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Proses Pengembalian Dana (Refund Transfer)</span>
                        </h2>

                        @if($return->refund)
                            <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50/50 space-y-3 font-body text-sm">
                                <div class="flex items-center justify-between border-b border-neutral-200 pb-2">
                                    <span class="font-bold text-neutral-800">Detail Bukti Transfer Refund</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $return->refund->status->badgeClass() }}">
                                        {{ $return->refund->status->label() }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div><span class="text-neutral-400 block">Bank Tujuan Pelanggan:</span> <span class="font-semibold text-neutral-800">{{ $return->refund->bank_name }}</span></div>
                                    <div><span class="text-neutral-400 block">Nomor & Pemilik Rekening:</span> <span class="font-mono font-bold text-neutral-800">{{ $return->refund->account_number }}</span> (a/n {{ $return->refund->account_name }})</div>
                                    <div><span class="text-neutral-400 block">Nominal Dana Dikembalikan:</span> <span class="font-bold text-emerald-700 text-sm">Rp{{ number_format((float) $return->refund->amount, 0, ',', '.') }}</span></div>
                                    <div><span class="text-neutral-400 block">No. Referensi Transfer:</span> <span class="font-mono text-neutral-800">{{ $return->refund->payment_reference }}</span></div>
                                    <div><span class="text-neutral-400 block">Waktu Transfer:</span> <span class="text-neutral-700">{{ $return->refund->transferred_at?->format('d M Y, H:i') ?? '-' }} WIB</span></div>
                                    @if($return->refund->confirmed_at)
                                        <div><span class="text-neutral-400 block">Dikonfirmasi Pelanggan:</span> <span class="font-semibold text-green-700">{{ $return->refund->confirmed_at->format('d M Y, H:i') }} WIB</span></div>
                                    @endif
                                </div>
                                @if($return->refund->proof_path)
                                    <div class="pt-2 border-t border-neutral-200">
                                        <a href="{{ route('secure.proof', ['type' => 'refund_proof', 'id' => $return->refund->id, 'filename' => basename($return->refund->proof_path)]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-primary-700 hover:underline font-semibold">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Berkas Bukti Transfer Bank</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-200 text-xs font-body text-neutral-500">
                                Belum ada mutasi refund yang dicatat. Dana refund dapat ditransfer setelah verifikasi fisik barang di gudang selesai.
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right Column: Customer Info, Order Info, Audit Trail -->
            <div class="space-y-6">
                <!-- Customer Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-5 space-y-3 font-body text-sm">
                    <h3 class="font-display text-sm font-bold uppercase tracking-wider text-neutral-500">
                        Data Pelanggan
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-neutral-400 block">Nama Lengkap:</span>
                            <span class="font-bold text-neutral-800">{{ $return->customer?->name ?? $return->order?->customer_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">No. WhatsApp / HP:</span>
                            <span class="font-mono text-neutral-800">{{ $return->customer?->phone ?? $return->order?->customer_phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">Email:</span>
                            <span class="text-neutral-700">{{ $return->customer?->email ?? $return->order?->customer_email ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">Alamat Kirim Asli:</span>
                            <span class="text-neutral-700 leading-relaxed block">
                                {{ $return->order?->address_line ?? $return->order?->shipping_address ?? '-' }}{{ ($return->order?->city || $return->order?->shipping_city) ? ', ' . ($return->order?->city ?? $return->order?->shipping_city) : '' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-5 space-y-3 font-body text-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-sm font-bold uppercase tracking-wider text-neutral-500">
                            Pesanan Terkait
                        </h3>
                        <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-xs text-primary-700 hover:underline font-semibold">
                            Buka Pesanan &rarr;
                        </a>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-neutral-400 block">Nomor Pesanan:</span>
                            <span class="font-mono font-bold text-neutral-800">{{ $return->order->order_number }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">Status Pesanan:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold {{ $return->order->status->badgeClass() }}">
                                {{ $return->order->status->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">Resi J&T Pesanan Asli:</span>
                            <span class="font-mono font-bold text-primary-700">{{ $return->order->tracking_number ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block">Total Pesanan:</span>
                            <span class="font-bold text-neutral-800">Rp{{ number_format((float) $return->order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status History Timeline -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-5 space-y-4 font-body text-sm">
                    <h3 class="font-display text-sm font-bold uppercase tracking-wider text-neutral-500">
                        Riwayat Perjalanan Retur
                    </h3>

                    <div class="relative pl-5 border-l-2 border-neutral-200 space-y-4">
                        @foreach($return->statusHistories as $history)
                            <div class="relative text-xs">
                                <span class="absolute -left-[27px] top-1 w-3 h-3 rounded-full bg-primary-700 ring-4 ring-white"></span>
                                <div class="font-semibold text-neutral-800">
                                    {{ \App\Enums\ReturnStatus::tryFrom($history->to_status)?->label() ?? $history->to_status }}
                                </div>
                                <div class="text-2xs text-neutral-400 mt-0.5">
                                    {{ $history->created_at->format('d M Y, H:i') }} WIB &bull; 
                                    <span class="capitalize">{{ $history->actor_type }}</span> ({{ $history->actor->name ?? '-' }})
                                </div>
                                @if($history->note)
                                    <p class="text-neutral-600 mt-1 p-2 rounded bg-neutral-50 border border-neutral-100 text-2xs">
                                        {{ $history->note }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Review Pengajuan Retur -->
    <div id="modal-review" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="font-display text-lg font-bold text-neutral-800">Tinjau Pengajuan Retur</h3>
                <button type="button" onclick="document.getElementById('modal-review').classList.add('hidden')" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.returns.review', $return) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Keputusan Review <span class="text-red-500">*</span></label>
                    <select name="action" id="review-action" required onchange="handleReviewActionChange(this.value)" class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-300 focus:border-primary-700 focus:outline-none">
                        <option value="approve">Setujui Pengajuan (Instruksikan Pelanggan Kirim Balik Barang)</option>
                        <option value="reject">Tolak Pengajuan (Klaim tidak valid / tidak sesuai syarat)</option>
                        <option value="need_info">Minta Informasi Tambahan (Video buram / foto kurang jelas)</option>
                    </select>
                </div>

                <div id="field-rejection-reason" class="hidden">
                    <label class="block font-body text-xs font-semibold text-red-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" placeholder="Jelaskan alasan sah penolakan klaim kepada pelanggan..." class="w-full p-3 rounded-lg font-body text-sm border border-red-300 focus:border-red-500 focus:outline-none"></textarea>
                </div>

                <div id="field-admin-notes">
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Catatan Admin / Petunjuk Pengiriman Balik</label>
                    <textarea name="admin_notes" rows="3" placeholder="Tuliskan petunjuk alamat gudang pengiriman J&T atau hal yang harus diperhatikan pembeli..." class="w-full p-3 rounded-lg font-body text-sm border border-neutral-300 focus:border-primary-700 focus:outline-none">Pengajuan retur disetujui. Silakan kirimkan produk lengkap ke Gudang RHS Heritage via J&T Express.</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
                    <x-button type="button" variant="outline" size="sm" onclick="document.getElementById('modal-review').classList.add('hidden')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Keputusan Review
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Konfirmasi Terima Barang Retur di Gudang -->
    <div id="modal-verify-received" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="font-display text-lg font-bold text-neutral-800">Verifikasi Barang Tiba</h3>
                <button type="button" onclick="document.getElementById('modal-verify-received').classList.add('hidden')" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.returns.verify-received', $return) }}" method="POST" class="space-y-4">
                @csrf
                <p class="font-body text-sm text-neutral-600">
                    Apakah paket barang retur dengan resi <span class="font-mono font-bold text-neutral-800">{{ $return->customerShipment()?->tracking_number }}</span> telah tiba di gudang dan selesai diverifikasi kelengkapannya?
                </p>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Catatan Kondisi Barang (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Kondisi kemasan utuh, label produk lengkap..." class="w-full p-2.5 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                    <x-button type="button" variant="outline" size="sm" onclick="document.getElementById('modal-verify-received').classList.add('hidden')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Ya, Konfirmasi Barang Tiba
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Kirim Barang Pengganti J&T -->
    <div id="modal-process-replacement" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="font-display text-lg font-bold text-neutral-800">Kirim Barang Pengganti (J&T Express)</h3>
                <button type="button" onclick="document.getElementById('modal-process-replacement').classList.add('hidden')" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.returns.process-replacement', $return) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-900 text-xs font-body space-y-1">
                    <p class="font-bold">Informasi Pengurangan Stok Otomatis</p>
                    <p>Sistem akan memotong kuantitas stok varian barang pengganti dari database secara otomatis setelah formulir ini disimpan.</p>
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Nomor Resi J&T Express Pengganti <span class="text-red-500">*</span></label>
                    <input type="text" name="tracking_number" required placeholder="Contoh: JT9827364512" class="w-full h-11 px-3 rounded-lg font-mono text-sm border border-neutral-300 focus:border-primary-700 focus:outline-none uppercase">
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Tanggal Pengiriman Barang Pengganti <span class="text-red-500">*</span></label>
                    <input type="date" name="shipping_date" required value="{{ date('Y-m-d') }}" class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-300 focus:border-primary-700 focus:outline-none">
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Foto Bukti Resi Fisik J&T <span class="text-red-500">*</span></label>
                    <input type="file" name="shipping_proof" required accept="image/jpeg,image/png,image/webp" class="w-full text-xs font-body border border-neutral-300 rounded-lg p-2 bg-neutral-50">
                    <p class="text-2xs text-neutral-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB)</p>
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Catatan Tambahan untuk Pembeli</label>
                    <textarea name="notes" rows="2" placeholder="Barang pengganti telah dipacking dengan bubble wrap tebal..." class="w-full p-2.5 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                    <x-button type="button" variant="outline" size="sm" onclick="document.getElementById('modal-process-replacement').classList.add('hidden')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Kirim Barang & Potong Stok
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Proses Transfer Refund -->
    <div id="modal-process-refund" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                <h3 class="font-display text-lg font-bold text-neutral-800">Proses Transfer Refund</h3>
                <button type="button" onclick="document.getElementById('modal-process-refund').classList.add('hidden')" class="text-neutral-400 hover:text-neutral-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @php
                $maxRefund = (float) $return->items->sum('subtotal');
            @endphp

            <form action="{{ route('admin.returns.process-refund', $return) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Bank Pengirim / Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" required placeholder="Contoh: BCA / Mandiri / BRI" class="w-full h-10 px-3 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Nomor Rekening Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" name="account_number" required placeholder="Nomor rekening penerima" class="w-full h-10 px-3 rounded-lg font-mono text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Atas Nama Rekening <span class="text-red-500">*</span></label>
                        <input type="text" name="account_name" required placeholder="Nama pemilik rekening" class="w-full h-10 px-3 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Nominal Refund (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="1" max="{{ $maxRefund }}" value="{{ (int) $maxRefund }}" class="w-full h-10 px-3 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none">
                        <span class="text-2xs text-neutral-400">Maksimal: Rp{{ number_format($maxRefund, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">No. Referensi Transfer / Mutasi Bank <span class="text-red-500">*</span></label>
                    <input type="text" name="payment_reference" required placeholder="Contoh: TRX-BCA-20261114-88912" class="w-full h-10 px-3 rounded-lg font-mono text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none">
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Upload Bukti Transfer Bank <span class="text-red-500">*</span></label>
                    <input type="file" name="proof" required accept="image/jpeg,image/png,image/webp,application/pdf" class="w-full text-xs font-body border border-neutral-300 rounded-lg p-2 bg-neutral-50">
                    <p class="text-2xs text-neutral-400 mt-1">Format: JPG, PNG, WEBP, PDF (Maksimal 5 MB)</p>
                </div>

                <div>
                    <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan pengembalian dana..." class="w-full p-2 rounded-lg font-body text-xs border border-neutral-300 focus:border-primary-700 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                    <x-button type="button" variant="outline" size="sm" onclick="document.getElementById('modal-process-refund').classList.add('hidden')">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Simpan Bukti & Proses Refund
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleReviewActionChange(action) {
            const rejectionDiv = document.getElementById('field-rejection-reason');
            const rejectionTextarea = rejectionDiv.querySelector('textarea');
            const adminNotesDiv = document.getElementById('field-admin-notes');

            if (action === 'reject') {
                rejectionDiv.classList.remove('hidden');
                rejectionTextarea.setAttribute('required', 'required');
            } else {
                rejectionDiv.classList.add('hidden');
                rejectionTextarea.removeAttribute('required');
            }

            if (action === 'need_info') {
                adminNotesDiv.querySelector('label').innerHTML = 'Catatan Informasi Tambahan yang Dibutuhkan <span class="text-red-500">*</span>';
                adminNotesDiv.querySelector('textarea').setAttribute('required', 'required');
                adminNotesDiv.querySelector('textarea').placeholder = 'Tuliskan informasi atau bukti apa yang belum lengkap...';
            } else {
                adminNotesDiv.querySelector('label').innerHTML = 'Catatan Admin / Petunjuk Pengiriman Balik';
                adminNotesDiv.querySelector('textarea').removeAttribute('required');
            }
        }
    </script>
</x-layouts.admin>
