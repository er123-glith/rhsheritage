<x-layouts.admin>
    <x-slot:title>Detail Pesanan #{{ $order->order_number }}</x-slot:title>

    <div class="space-y-6" x-data="{ cancelModalOpen: false, shipModalOpen: false }">
        <!-- Breadcrumb & Back -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 print:hidden">
            <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('admin.orders.index') }}" class="hover:text-primary-700 transition">Pesanan Masuk</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-700 font-medium font-mono">#{{ $order->order_number }}</span>
            </nav>

            <div class="flex items-center gap-2">
                <button 
                    onclick="window.print()" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 bg-white text-neutral-700 font-body text-xs font-semibold hover:bg-neutral-50 transition shadow-xs"
                >
                    <svg class="w-4 h-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Invoice / Label</span>
                </button>
            </div>
        </div>

        <!-- Order Header & Status Action Bar -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">#{{ $order->order_number }}</h1>
                    <x-status-badge :status="$order->status" class="text-sm px-3.5 py-1" />
                </div>
                <p class="font-body text-xs text-neutral-500 mt-1.5">
                    Dibuat pada {{ $order->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                    @if($order->confirmed_at)
                        • Dikonfirmasi pada {{ $order->confirmed_at->translatedFormat('d M Y - H:i') }} WIB
                    @endif
                </p>
            </div>

            <!-- Action Controls according to state machine -->
            <div class="flex items-center gap-2 flex-wrap print:hidden">
                @if($order->status === \App\Enums\OrderStatus::MenungguKonfirmasi)
                    <!-- Confirm Order Button -->
                    <form method="POST" action="{{ route('admin.orders.confirm', $order) }}" data-confirm="Konfirmasi pesanan ini? Stok seluruh item varian akan otomatis dipotong.">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-600 text-neutral-900 font-body text-sm font-bold shadow-1 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Konfirmasi Pesanan</span>
                        </button>
                    </form>

                    <!-- Cancel Order Button -->
                    <button 
                        @click="cancelModalOpen = true" 
                        type="button" 
                        class="px-4 py-2.5 rounded-lg border border-danger/40 text-danger hover:bg-danger-bg font-body text-sm font-semibold transition"
                    >
                        Batalkan Pesanan
                    </button>

                @elseif($order->status === \App\Enums\OrderStatus::Diproses)
                    <!-- Ship Order Button -->
                    <button 
                        @click="shipModalOpen = true" 
                        type="button" 
                        class="px-4 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-bold shadow-1 transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <span>Kirim & Input Resi</span>
                    </button>

                    <!-- Cancel Button (restores stock!) -->
                    <button 
                        @click="cancelModalOpen = true" 
                        type="button" 
                        class="px-4 py-2.5 rounded-lg border border-danger/40 text-danger hover:bg-danger-bg font-body text-sm font-semibold transition"
                    >
                        Batalkan Pesanan
                    </button>

                @elseif($order->status === \App\Enums\OrderStatus::Dikirim)
                    <!-- Complete Order Button -->
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" data-confirm="Tandai pesanan ini telah selesai diterima pelanggan?">
                        @csrf
                        <input type="hidden" name="target_status" value="{{ \App\Enums\OrderStatus::Selesai->value }}">
                        <button type="submit" class="px-4 py-2.5 rounded-lg bg-success hover:opacity-90 text-white font-body text-sm font-bold shadow-1 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Tandai Selesai</span>
                        </button>
                    </form>

                @elseif($order->status === \App\Enums\OrderStatus::Selesai)
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-primary-50 text-success text-xs font-bold border border-primary-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Pesanan Selesai
                    </span>

                @elseif($order->status === \App\Enums\OrderStatus::Dibatalkan)
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-danger-bg text-danger text-xs font-bold border border-danger/20">
                        Pesanan Dibatalkan
                    </span>
                @endif
            </div>
        </div>

        @if($order->status === \App\Enums\OrderStatus::Dibatalkan && $order->cancellation_reason)
            <!-- Cancellation Notice Banner -->
            <div class="rounded-xl bg-danger-bg border border-danger/30 p-4 text-danger flex items-start gap-3">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="font-body text-xs uppercase font-bold tracking-wider">Alasan Pembatalan Pesanan:</p>
                    <p class="font-body text-sm mt-0.5 font-medium">{{ $order->cancellation_reason }}</p>
                </div>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Items & Timeline (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ordered Items Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                        <h2 class="font-display text-base font-bold text-primary-700">Daftar Produk Dipesan</h2>
                        <span class="text-xs text-neutral-500 font-medium font-body">{{ $order->items->count() }} varian item</span>
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
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-md bg-neutral-100 border border-neutral-200 overflow-hidden shrink-0 flex items-center justify-center">
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
                    <div class="p-6 bg-canvas-sunken/40 border-t border-neutral-200 space-y-2">
                        <div class="flex justify-between text-sm font-body text-neutral-600">
                            <span>Subtotal Produk</span>
                            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-body text-neutral-600">
                            <span>Ongkos Kirim ({{ $order->shipping_zone_name ?? 'Reguler' }} • {{ number_format($order->total_weight_gram) }} gr)</span>
                            <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-body font-bold text-primary-700 pt-3 border-t border-neutral-200">
                            <span>Total Pembayaran</span>
                            <span class="text-lg">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline Log -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6">
                    <h2 class="font-display text-base font-bold text-primary-700 mb-4">Riwayat Status & Log Transaksi</h2>
                    
                    <div class="relative pl-6 border-l-2 border-primary-200 space-y-6">
                        @forelse($order->statusLogs as $log)
                            <div class="relative group">
                                <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-primary-700 border-2 border-white ring-4 ring-primary-100"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-body text-xs font-bold text-primary-800 uppercase tracking-wider">
                                            {{ \App\Enums\OrderStatus::tryFrom($log->to_status)?->label() ?? $log->to_status }}
                                        </span>
                                        <span class="text-xs text-neutral-400 font-body">
                                            {{ $log->created_at ? $log->created_at->translatedFormat('d M Y - H:i:s') : '-' }} WIB
                                        </span>
                                    </div>
                                    @if($log->note)
                                        <p class="font-body text-xs text-neutral-600 mt-1 bg-canvas-sunken p-2.5 rounded-lg border border-neutral-200/60">
                                            {{ $log->note }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-neutral-400">Belum ada riwayat status tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Customer Info & Shipping (1 col) -->
            <div class="space-y-6">
                <!-- Customer Details Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h2 class="font-display text-base font-bold text-primary-700">Data Pelanggan</h2>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Nama Lengkap</p>
                        <p class="text-sm font-bold text-neutral-800 mt-0.5">{{ $order->customer_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Status Akun Customer</p>
                        @if($order->user)
                            <div class="mt-1 p-2.5 rounded-lg bg-primary-50 border border-primary-200">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-primary-700 text-white">
                                        Member Terdaftar
                                    </span>
                                    <a href="{{ route('admin.customers.show', $order->user) }}" class="text-xs font-semibold text-primary-700 hover:text-primary-900 transition flex items-center gap-1">
                                        <span>Lihat Profil</span>
                                        <span>↗</span>
                                    </a>
                                </div>
                                <p class="text-xs text-neutral-600 font-mono mt-1.5">{{ $order->user->email }}</p>
                            </div>
                        @else
                            <div class="mt-1 p-2.5 rounded-lg bg-neutral-100 border border-neutral-200 text-xs text-neutral-600">
                                <span class="font-semibold text-neutral-700">Tamu / Pelanggan Lama</span>
                                <p class="text-[11px] text-neutral-400 mt-0.5">Pesanan dibuat sebelum sistem multi-role atau tanpa login.</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Nomor Telepon / WhatsApp</p>
                        <p class="text-sm font-mono font-medium text-neutral-800 mt-0.5">{{ $order->customer_phone }}</p>
                    </div>

                    <!-- Direct Chat via WhatsApp Button -->
                    <a 
                        href="{{ $customerWaUrl }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#25D366] hover:bg-[#1EBE5D] text-white font-body text-xs font-bold transition shadow-xs"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Chat Pelanggan di WhatsApp</span>
                    </a>
                </div>

                <!-- Shipping Address Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h2 class="font-display text-base font-bold text-primary-700">Alamat Pengiriman</h2>
                        <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
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
                        <div class="p-3.5 rounded-lg bg-primary-50 border border-primary-200">
                            <p class="text-xs uppercase tracking-wider text-primary-700 font-bold font-body">Nomor Resi Pengiriman</p>
                            <p class="text-base font-mono font-bold text-primary-900 mt-1 tracking-wider">{{ $order->tracking_number }}</p>
                        </div>
                    @endif

                    @if($order->note)
                        <div class="pt-3 border-t border-neutral-100">
                            <p class="text-xs uppercase tracking-wider text-neutral-400 font-semibold font-body">Catatan dari Pelanggan</p>
                            <p class="text-xs italic text-neutral-700 mt-1 bg-canvas-sunken p-2.5 rounded-lg border border-neutral-200/60">
                                "{{ $order->note }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal: Batalkan Pesanan -->
        <div 
            x-show="cancelModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="cancelModalOpen = false"
                class="bg-white rounded-xl max-w-md w-full p-6 shadow-3"
            >
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200">
                    <h3 class="font-display text-lg font-bold text-danger">Batalkan Pesanan</h3>
                    <button @click="cancelModalOpen = false" class="text-neutral-400 hover:text-neutral-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="mt-4 space-y-4">
                    @csrf
                    <p class="font-body text-xs text-neutral-600 leading-relaxed">
                        @if($order->status === \App\Enums\OrderStatus::Diproses)
                            Pesanan ini sudah dikonfirmasi. Membatalkannya akan <strong class="text-neutral-900">mengembalikan stok varian</strong> ke persediaan toko.
                        @else
                            Pesanan akan ditandai dibatalkan dan pelanggan dapat melihat alasan pembatalan ini pada halaman pelacakan pesanan.
                        @endif
                    </p>

                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">
                            Alasan Pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea
                            name="cancellation_reason"
                            rows="3"
                            required
                            placeholder="Contoh: Stok habis, alamat di luar jangkauan, atau permintaan pembeli."
                            class="w-full p-3 rounded-lg font-body text-sm border border-neutral-300 focus:outline-none focus:border-danger focus:ring-1 focus:ring-danger"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="cancelModalOpen = false" 
                            class="px-4 py-2 rounded-lg font-body text-xs font-semibold text-neutral-600 hover:bg-neutral-100"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg font-body text-xs font-bold bg-danger hover:opacity-90 text-white shadow-xs"
                        >
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Kirim & Input Resi -->
        <div 
            x-show="shipModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.outside="shipModalOpen = false"
                class="bg-white rounded-xl max-w-md w-full p-6 shadow-3"
            >
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200">
                    <h3 class="font-display text-lg font-bold text-primary-700">Kirim Pesanan & Input Resi</h3>
                    <button @click="shipModalOpen = false" class="text-neutral-400 hover:text-neutral-600">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="target_status" value="{{ \App\Enums\OrderStatus::Dikirim->value }}">

                    <p class="font-body text-xs text-neutral-600 leading-relaxed">
                        Ubah status pesanan menjadi <strong>Dikirim</strong>. Masukkan nomor resi ekspedisi jika paket sudah diserahkan ke kurir / ekspedisi pengiriman.
                    </p>

                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1">
                            Nomor Resi / Kurir (Opsional)
                        </label>
                        <input
                            type="text"
                            name="tracking_number"
                            value="{{ $order->tracking_number }}"
                            placeholder="Contoh: JNE1234567890 atau JNT987654321"
                            class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-1 focus:ring-primary-700 font-mono"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="shipModalOpen = false" 
                            class="px-4 py-2 rounded-lg font-body text-xs font-semibold text-neutral-600 hover:bg-neutral-100"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg font-body text-xs font-bold bg-primary-700 hover:bg-primary-800 text-white shadow-xs"
                        >
                            Simpan & Tandai Dikirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
