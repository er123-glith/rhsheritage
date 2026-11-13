<x-layouts.customer>
    <x-slot:title>Detail Pesanan #{{ $order->order_number }}</x-slot:title>

    <div class="space-y-6" x-data="{ confirmDeliveryModalOpen: false }">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 print:hidden">
            <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('customer.orders') }}" class="hover:text-primary-700 transition">Pesanan Saya</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-700 font-medium font-mono">#{{ $order->order_number }}</span>
            </nav>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Muat Ulang Status (Manual Refresh Tracking) -->
                <button 
                    onclick="window.location.reload()" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary-300 bg-primary-50 text-primary-800 font-body text-xs font-semibold hover:bg-primary-100 transition shadow-xs"
                    title="Perbarui status pesanan terbaru"
                >
                    <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Muat Ulang Status</span>
                </button>

                @if($order->status === \App\Enums\OrderStatus::Dikirim && ! $order->hasActiveReturn())
                    <!-- Tombol Konfirmasi Barang Diterima -->
                    <button 
                        @click="confirmDeliveryModalOpen = true" 
                        type="button" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-body text-xs font-bold transition shadow-xs"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Konfirmasi Barang Diterima</span>
                    </button>
                @endif

                @if(in_array($order->status, [\App\Enums\OrderStatus::Dikirim, \App\Enums\OrderStatus::Selesai]) && ! $order->hasActiveReturn())
                    <!-- Tombol Ajukan Retur -->
                    <a 
                        href="{{ route('customer.returns.create', $order->order_number) }}" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-900 font-body text-xs font-semibold hover:bg-amber-100 transition shadow-xs"
                    >
                        <svg class="w-4 h-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        <span>Ajukan Retur</span>
                    </a>
                @endif

                @if($order->status === \App\Enums\OrderStatus::Selesai)
                    @if($order->review)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-body text-xs font-semibold">
                            <i class="fa-solid fa-star text-gold-500"></i>
                            <span>Sudah Diulas ({{ $order->review->rating }}★)</span>
                        </span>
                    @else
                        <a 
                            href="{{ route('customer.reviews.create', $order->order_number) }}" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs font-bold transition shadow-xs"
                        >
                            <i class="fa-solid fa-star text-neutral-900"></i>
                            <span>Beri Ulasan Toko</span>
                        </a>
                    @endif
                @endif

                <a 
                    href="{{ route('customer.orders.invoice', $order->order_number) }}" 
                    target="_blank"
                    class="inline-flex items-center px-3.5 py-1.5 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 font-body text-xs font-semibold hover:bg-neutral-50 dark:hover:bg-neutral-700 transition shadow-xs"
                >
                    <span>Cetak Invoice Resmi (PDF)</span>
                </a>
            </div>
        </div>

        <!-- Order Header & Status Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">#{{ $order->order_number }}</h1>
                    <x-status-badge :status="$order->status" class="text-sm px-3.5 py-1" />
                </div>
                <p class="font-body text-xs text-neutral-500 mt-1.5">
                    Dipesan pada {{ $order->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                    @if($order->confirmed_at)
                        • Dikonfirmasi pada {{ $order->confirmed_at->translatedFormat('d M Y - H:i') }} WIB
                    @endif
                </p>
            </div>

            <!-- WhatsApp Customer Care Button -->
            <div class="print:hidden">
                <a 
                    href="{{ $whatsappUrl }}" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#25D366] hover:bg-[#1EBE5D] text-white font-body text-xs font-bold transition shadow-xs"
                >
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Konfirmasi / Tanya via WhatsApp</span>
                </a>
            </div>
        </div>

        @if($order->status === \App\Enums\OrderStatus::Dibatalkan && $order->cancellation_reason)
            <!-- Cancellation Notice -->
            <div class="rounded-xl bg-danger-bg border border-danger/30 p-4 text-danger flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-body text-xs uppercase font-bold tracking-wider">Catatan Pembatalan:</p>
                    <p class="font-body text-sm mt-0.5 font-medium">{{ $order->cancellation_reason }}</p>
                </div>
            </div>
        @endif

        @if($order->status === \App\Enums\OrderStatus::Selesai)
            @if($order->review)
                <!-- Customer Review Card -->
                <div class="rounded-xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 shadow-1 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-100 dark:border-neutral-800">
                        <div class="flex items-center gap-2">
                            <span class="text-gold-500 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $order->review->rating ? 'fa-solid fa-star' : 'fa-regular fa-star text-neutral-300 dark:text-neutral-600' }}"></i>
                                @endfor
                            </span>
                            <span class="font-bold text-sm text-neutral-800 dark:text-white">{{ $order->review->rating }}.0 / 5.0</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->review->moderation_status_badge_class }}">
                                {{ $order->review->moderation_status_label }}
                            </span>
                            <span class="text-xs text-neutral-400">{{ $order->review->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="font-body text-xs uppercase tracking-wider font-bold text-neutral-400 mb-1">Ulasan Toko Anda:</p>
                        <p class="font-body text-sm text-neutral-700 dark:text-neutral-200 italic leading-relaxed">
                            "{{ $order->review->review }}"
                        </p>
                        @if($order->review->moderation_status === \App\Models\Review::STATUS_PENDING_REVIEW)
                            <p class="mt-2 text-xs text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock"></i>
                                <span>Ulasan ini sedang dalam antrean tinjauan tim moderasi admin sebelum ditampilkan di beranda utama.</span>
                            </p>
                        @elseif($order->review->moderation_status === \App\Models\Review::STATUS_PUBLISHED)
                            <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Ulasan Anda aktif dan dipublikasikan pada bagian Ulasan Pelanggan di beranda utama.</span>
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <!-- Review Invitation Banner -->
                <div class="rounded-xl bg-gradient-to-r from-gold-500/10 via-primary-500/10 to-transparent border border-gold-400/40 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-display text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-star text-gold-500"></i>
                            <span>Bagikan Pengalaman Belanja Anda</span>
                        </h3>
                        <p class="font-body text-xs text-neutral-600 dark:text-neutral-300 mt-1">
                            Pesanan Anda telah selesai diterima. Ceritakan kualitas kriya Songkok / Sarung / Jilbab RHS Heritage untuk membantu pembeli lain.
                        </p>
                    </div>
                    <a 
                        href="{{ route('customer.reviews.create', $order->order_number) }}" 
                        class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs font-bold transition shadow-xs shrink-0 text-center"
                    >
                        Tulis Ulasan Sekarang &rarr;
                    </a>
                </div>
            @endif
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Items & Timeline (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ordered Items Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                        <h2 class="font-display text-base font-bold text-primary-700">Rincian Produk</h2>
                        <span class="text-xs text-neutral-500 font-medium font-body">{{ $order->orderItems->count() }} item</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-canvas-sunken border-b border-neutral-200">
                                <tr>
                                    <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Item Produk</th>
                                    <th class="px-4 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Harga Satuan</th>
                                    <th class="px-4 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center">Jumlah</th>
                                    <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-md bg-neutral-100 border border-neutral-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                    @if($item->variant?->product?->primaryImage)
                                                        <img 
                                                            src="{{ $item->variant->product->primaryImage->url }}" 
                                                            alt="{{ $item->product_name }}" 
                                                            class="w-full h-full object-cover"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                        >
                                                        <div class="hidden w-full h-full items-center justify-center bg-neutral-100 text-neutral-400">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-neutral-100 text-neutral-400">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-body text-sm font-semibold text-neutral-800">
                                                        {{ $item->product_name }}
                                                    </div>
                                                    <div class="text-xs text-neutral-500 font-mono mt-0.5">
                                                        {{ $item->variant_label }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 font-body text-sm text-neutral-600">
                                            Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 font-body text-sm font-semibold text-neutral-800 text-center">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 font-body text-sm font-bold text-neutral-800 text-right">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Footer -->
                    <div class="bg-canvas-sunken/60 p-6 border-t border-neutral-200">
                        <div class="max-w-xs ml-auto space-y-2 font-body text-sm">
                            <div class="flex justify-between text-neutral-600">
                                <span>Subtotal Produk</span>
                                <span class="font-medium text-neutral-800">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-neutral-600">
                                <span>Ongkos Kirim ({{ number_format($order->total_weight_gram) }} gr)</span>
                                <span class="font-medium text-neutral-800">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-primary-700 pt-3 border-t border-neutral-200">
                                <span>Total Pembayaran</span>
                                <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline & Activity Log -->
                <x-order.progress-timeline :order="$order" context="customer" />
            </div>

            <!-- Right Column: Shipping & Info (1 col) -->
            <div class="space-y-6">
                <!-- Shipping Address Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h2 class="font-display text-base font-bold text-primary-700">Tujuan Pengiriman</h2>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Penerima</p>
                        <p class="text-sm font-bold text-neutral-800 mt-0.5">{{ $order->customer_name }}</p>
                        <p class="text-xs font-mono text-neutral-500 mt-0.5">{{ $order->customer_phone }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Alamat Lengkap</p>
                        <p class="text-sm text-neutral-800 mt-0.5 leading-relaxed">{{ $order->address_line }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Kota / Kab.</p>
                            <p class="font-medium text-neutral-800 mt-0.5">{{ $order->city }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Provinsi</p>
                            <p class="font-medium text-neutral-800 mt-0.5">{{ $order->province }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Kode Pos</p>
                            <p class="font-mono text-neutral-800 mt-0.5">{{ $order->postal_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Zona Ongkir</p>
                            <p class="font-medium text-neutral-800 mt-0.5">{{ $order->shipping_zone_name ?? '-' }}</p>
                        </div>
                    </div>

                    @if($order->tracking_number)
                        <div class="p-4 rounded-xl bg-primary-50 border border-primary-200 space-y-3 font-body">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-primary-700">Ekspedisi Pengiriman</span>
                                <span class="px-2 py-0.5 rounded bg-primary-700 text-white font-bold text-[10px]">J&T Express</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-neutral-500 uppercase tracking-wider">Nomor Resi J&T</p>
                                <p class="text-base font-mono font-bold text-primary-900 tracking-wider">{{ $order->tracking_number }}</p>
                            </div>
                            @if($order->shipping_date)
                                <div>
                                    <p class="text-[10px] text-neutral-500 uppercase tracking-wider">Tanggal Kirim</p>
                                    <p class="text-xs font-semibold text-neutral-800">{{ $order->shipping_date->translatedFormat('d F Y') }}</p>
                                </div>
                            @endif
                            @if($order->shipping_proof_path)
                                <div>
                                    <p class="text-[10px] text-neutral-500 uppercase tracking-wider mb-1">Bukti Struk Resi</p>
                                    <a 
                                        href="{{ route('secure.proof', ['type' => 'shipping_proof', 'id' => $order->id, 'filename' => basename($order->shipping_proof_path)]) }}" 
                                        target="_blank" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-primary-300 text-primary-800 hover:bg-primary-100 text-xs font-semibold transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat Foto Bukti Resi</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($order->delivered_at)
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 space-y-2.5 font-body">
                            <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Barang Telah Diterima</span>
                            </div>
                            <p class="text-xs text-neutral-600">
                                Dikonfirmasi pada {{ $order->delivered_at->translatedFormat('d M Y - H:i') }} WIB
                            </p>
                            @if($order->delivery_proof_path)
                                <a 
                                    href="{{ route('secure.proof', ['type' => 'delivery_proof', 'id' => $order->id, 'filename' => basename($order->delivery_proof_path)]) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Lihat Foto Bukti Penerimaan</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($order->returns->isNotEmpty())
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 space-y-2 font-body">
                            <span class="text-xs font-bold text-amber-900 block flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Pengajuan Retur Anda</span>
                            </span>
                            @foreach($order->returns as $ret)
                                <div class="p-2.5 bg-white rounded-lg border border-amber-200 text-xs flex items-center justify-between">
                                    <div>
                                        <p class="font-bold font-mono text-neutral-800">{{ $ret->return_number }}</p>
                                        <p class="text-[11px] text-neutral-500">{{ $ret->solutionLabel() }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border {{ $ret->status->badgeClasses() }}">
                                            {{ $ret->status->label() }}
                                        </span>
                                        <a href="{{ route('customer.returns.show', $ret->return_number) }}" class="block text-[11px] text-primary-700 font-semibold hover:underline mt-0.5">Lihat Retur →</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($order->note)
                        <div class="pt-3 border-t border-neutral-100">
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Catatan Anda</p>
                            <p class="text-xs italic text-neutral-700 mt-1 bg-canvas-sunken p-2.5 rounded-lg border border-neutral-200/60">
                                "{{ $order->note }}"
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Assistance Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-3">
                    <h3 class="font-display text-sm font-bold text-primary-700">Butuh Bantuan Pesanan?</h3>
                    <p class="font-body text-xs text-neutral-500 leading-relaxed">
                        Jika ada pertanyaan atau kendala pengiriman, tim kami siap membantu melalui WhatsApp.
                    </p>
                    <a 
                        href="{{ $whatsappUrl }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-semibold transition"
                    >
                        <span>Hubungi Admin RHS Heritage</span>
                        <span class="text-xs">↗</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal: Konfirmasi Barang Diterima -->
        <div 
            x-show="confirmDeliveryModalOpen" 
            x-cloak
            x-data="{
                fileName: '',
                fileSizeFormatted: '',
                fileError: '',
                isOverLimit: false,
                maxBytes: {{ config('uploads.delivery_proof.max_bytes', 5242880) }},
                maxMb: {{ config('uploads.delivery_proof.max_mb', 5) }},
                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        this.resetFile();
                        return;
                    }
                    this.fileName = file.name;
                    const sizeKb = file.size / 1024;
                    this.fileSizeFormatted = sizeKb < 1024
                        ? sizeKb.toFixed(1) + ' KB'
                        : (sizeKb / 1024).toFixed(2) + ' MB';

                    if (file.size > this.maxBytes) {
                        this.isOverLimit = true;
                        this.fileError = 'Ukuran file (' + this.fileSizeFormatted + ') melebihi batas maksimal ' + this.maxMb + ' MB. Silakan pilih foto lain atau kompres file terlebih dahulu.';
                    } else {
                        this.isOverLimit = false;
                        this.fileError = '';
                    }
                },
                resetFile() {
                    this.fileName = '';
                    this.fileSizeFormatted = '';
                    this.fileError = '';
                    this.isOverLimit = false;
                    if (this.$refs.deliveryProofInput) {
                        this.$refs.deliveryProofInput.value = '';
                    }
                },
                handleSubmit(e) {
                    if (this.isOverLimit) {
                        e.preventDefault();
                        return false;
                    }
                }
            }"
            class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="confirmDeliveryModalOpen = false"
                class="bg-white rounded-xl max-w-md w-full p-6 shadow-3 font-body"
            >
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200">
                    <h3 class="font-display text-lg font-bold text-primary-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Konfirmasi Penerimaan Barang</span>
                    </h3>
                    <button @click="confirmDeliveryModalOpen = false" class="text-neutral-400 hover:text-neutral-600">✕</button>
                </div>

                <form method="POST" action="{{ route('customer.orders.confirm-delivery', $order->order_number) }}" enctype="multipart/form-data" @submit="handleSubmit($event)" class="mt-4 space-y-4">
                    @csrf

                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Pastikan Anda telah membuka dan memeriksa fisik barang pesanan sebelum mengonfirmasi. Setelah dikonfirmasi, status pesanan akan diselesaikan.
                    </p>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 mb-1">
                            Foto Bukti Penerimaan Barang <span class="text-danger">*</span>
                            <span class="text-neutral-400 font-normal ml-1">(JPG, PNG, WEBP maks {{ config('uploads.delivery_proof.max_mb', 5) }} MB)</span>
                        </label>
                        <input
                            x-ref="deliveryProofInput"
                            @change="handleFileSelect($event)"
                            type="file"
                            name="delivery_proof"
                            required
                            accept="{{ config('uploads.delivery_proof.accept', 'image/jpeg,image/png,image/webp') }}"
                            class="w-full text-xs text-neutral-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                        />

                        <!-- File Info & Error Preview -->
                        <template x-if="fileName">
                            <div class="mt-2 p-2.5 rounded-lg border text-xs flex items-center justify-between"
                                :class="isOverLimit ? 'bg-red-50 border-red-200 text-red-700' : 'bg-primary-50 border-primary-200 text-primary-800'">
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <span x-text="isOverLimit ? '⚠️' : '✓'" class="font-bold"></span>
                                    <span class="font-medium truncate" x-text="fileName"></span>
                                    <span class="text-2xs opacity-75 whitespace-nowrap" x-text="'(' + fileSizeFormatted + ')'"></span>
                                </div>
                                <button 
                                    type="button" 
                                    @click="resetFile()" 
                                    class="text-xs font-semibold hover:underline shrink-0"
                                    :class="isOverLimit ? 'text-red-700' : 'text-primary-700'"
                                >
                                    Ganti
                                </button>
                            </div>
                        </template>

                        <!-- Inline Error Message -->
                        <template x-if="fileError">
                            <p class="mt-1.5 text-xs text-danger font-medium flex items-center gap-1" x-text="fileError"></p>
                        </template>
                        @error('delivery_proof')
                            <p class="mt-1 text-xs text-danger font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                name="agreement"
                                value="1"
                                required
                                class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                            />
                            <span class="text-xs font-medium text-neutral-800 leading-snug">
                                Saya menyatakan bahwa barang pesanan telah diterima dan sesuai dengan pesanan saya.
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-neutral-100">
                        <button 
                            type="button" 
                            @click="confirmDeliveryModalOpen = false" 
                            class="px-4 py-2 rounded-lg text-xs font-semibold text-neutral-600 hover:bg-neutral-100"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isOverLimit"
                            :class="isOverLimit ? 'opacity-50 cursor-not-allowed bg-neutral-400' : 'bg-emerald-600 hover:bg-emerald-700'"
                            class="px-5 py-2 rounded-lg text-xs font-bold text-white shadow-xs transition"
                        >
                            Konfirmasi Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.customer>

