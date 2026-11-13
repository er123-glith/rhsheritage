<x-layouts.public>
    <x-slot:title>Lacak Status Pesanan — RHS HERITAGE</x-slot:title>

    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Breadcrumbs & Heading -->
        <div class="text-center space-y-2" data-aos="fade-down" data-aos-duration="600">
            <nav class="flex items-center justify-center gap-2 text-xs font-body text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                <span>/</span>
                <span class="text-primary-700 font-semibold">Lacak Pesanan</span>
            </nav>

            <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800">
                Lacak Status Pesanan
            </h1>
            <p class="font-body text-xs sm:text-sm text-neutral-500 max-w-md mx-auto">
                Demi keamanan data privasi Anda, masukkan Nomor Pesanan dan Nomor WhatsApp yang digunakan saat pemesanan.
            </p>
        </div>

        <!-- Search Form Card -->
        <div class="rounded-2xl bg-white p-6 sm:p-8 border border-neutral-200 shadow-1" data-aos="fade-up" data-aos-duration="650">
            <form action="{{ route('order.track.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                            Nomor Pesanan <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="order_number"
                            value="{{ old('order_number', isset($order) ? $order->order_number : '') }}"
                            required
                            placeholder="Contoh: RHS-20260826-0001"
                            class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken font-mono text-sm text-neutral-800 uppercase border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                        >
                        @error('order_number')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                            Nomor WhatsApp Pemesan <span class="text-danger">*</span>
                        </label>
                        <input
                            type="tel"
                            name="customer_phone"
                            value="{{ old('customer_phone', isset($order) ? $order->customer_phone : '') }}"
                            required
                            placeholder="Contoh: 08123456789"
                            class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                        >
                        @error('customer_phone')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cek Status Pesanan
                    </x-button>
                </div>
            </form>
        </div>

        <!-- Result Display -->
        @if(isset($order))
            <div class="rounded-2xl bg-white p-6 sm:p-8 border border-neutral-200 shadow-2 space-y-6" data-aos="fade-up" data-aos-duration="650">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-neutral-100 pb-4">
                    <div>
                        <span class="font-body text-xs text-neutral-500 block">Status Terkini:</span>
                        <p class="font-display text-xl font-bold text-neutral-800">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <x-status-badge :status="$order->status" />
                    </div>
                </div>

                <!-- Resi Info if Available -->
                @if($order->tracking_number)
                    <div class="p-4 rounded-xl bg-gold-50 border border-gold-300 text-gold-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2" data-aos="zoom-in" data-aos-delay="100">
                        <div>
                            <span class="font-body text-xs font-semibold uppercase tracking-wider text-gold-700 block">
                                Nomor Resi Ekspedisi:
                            </span>
                            <span class="font-mono text-base font-bold">{{ $order->tracking_number }}</span>
                        </div>
                        <span class="font-body text-xs text-gold-800">
                            Ekspedisi: {{ $order->courier_name ?? $order->shipping_zone_name }}
                        </span>
                    </div>
                @endif

                <!-- Status Timeline History -->
                @if($order->statusLogs && $order->statusLogs->isNotEmpty())
                    <div>
                        <h4 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-500 mb-4">
                            Riwayat Perjalanan Pesanan
                        </h4>
                        <div class="space-y-4 pl-3 border-l-2 border-primary-200">
                            @foreach($order->statusLogs as $index => $log)
                                <div class="relative" data-aos="fade-left" data-aos-delay="{{ 100 * ($index + 1) }}">
                                    <span class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-primary-700 ring-4 ring-white"></span>
                                    <div class="font-body text-xs">
                                        <span class="font-bold text-neutral-800">{{ $log->status->label() ?? $log->status }}</span>
                                        <span class="text-neutral-400 ml-2">{{ $log->created_at->format('d M Y, H:i') }} WIB</span>
                                        @if($log->notes)
                                            <p class="text-neutral-600 mt-0.5">{{ $log->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Items Purchased -->
                <div class="pt-4 border-t border-neutral-100">
                    <h4 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-500 mb-3">
                        Item yang Dipesan
                    </h4>
                    <div class="divide-y divide-neutral-100">
                        @foreach($order->orderItems as $item)
                            <div class="py-2.5 flex items-center justify-between font-body text-xs sm:text-sm">
                                <div>
                                    <p class="font-semibold text-neutral-800">{{ $item->product_name }}</p>
                                    <p class="text-neutral-500 text-xs">{{ $item->variant_label }} &times; {{ $item->quantity }} pcs</p>
                                </div>
                                <span class="font-semibold text-neutral-700">
                                    Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Total -->
                <div class="pt-3 border-t border-neutral-100 flex items-center justify-between font-body">
                    <span class="text-xs text-neutral-500">Total Pembayaran:</span>
                    <span class="font-bold text-lg text-primary-700">
                        Rp{{ number_format((float) $order->total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</x-layouts.public>
