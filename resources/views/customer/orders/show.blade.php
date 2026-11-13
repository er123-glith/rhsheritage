<x-layouts.customer>
    <x-slot:title>Detail Pesanan #{{ $order->order_number }}</x-slot:title>

    <div class="space-y-6">
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

            <div class="flex items-center gap-2">
                <button 
                    onclick="window.print()" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 bg-white text-neutral-700 font-body text-xs font-semibold hover:bg-neutral-50 transition shadow-xs"
                >
                    <svg class="w-4 h-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Bukti Pesanan</span>
                </button>
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

                <!-- Status Timeline Card -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6">
                    <h2 class="font-display text-base font-bold text-primary-700 mb-4">Perkembangan Pesanan</h2>

                    <div class="space-y-4">
                        @forelse($order->statusLogs as $log)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">
                                    ✓
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="font-body text-sm font-semibold text-neutral-800">
                                            Status diubah menjadi: <span class="text-primary-700 font-bold">{{ $log->to_status?->label() ?? $log->to_status }}</span>
                                        </p>
                                        <span class="font-body text-xs text-neutral-400">
                                            {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB
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
                            <p class="text-xs text-neutral-400">Belum ada pembaruan status log.</p>
                        @endforelse
                    </div>
                </div>
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
                        <div class="p-3.5 rounded-lg bg-primary-50 border border-primary-200">
                            <p class="text-xs uppercase tracking-wider text-primary-700 font-bold font-body">Nomor Resi Pengiriman</p>
                            <p class="text-base font-mono font-bold text-primary-900 mt-1 tracking-wider">{{ $order->tracking_number }}</p>
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
    </div>
</x-layouts.customer>

