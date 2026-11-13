<x-layouts.public>
    <x-slot:title>Pesanan Saya — RHS HERITAGE</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumbs & Heading -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-aos="fade-down" data-aos-duration="600">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-500 mb-1">
                    <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">Pesanan Saya</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800">
                    Riwayat Pesanan Saya
                </h1>
                <p class="font-body text-xs sm:text-sm text-neutral-500 mt-1">
                    Daftar seluruh pesanan kriya yang pernah Anda buat di RHS HERITAGE.
                </p>
            </div>
            <div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-semibold shadow-sm transition">
                    <span>Belanja Lagi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        @if($orders->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center border border-neutral-200 shadow-1 space-y-4" data-aos="fade-up">
                <div class="w-16 h-16 rounded-full bg-primary-50 text-primary-700 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="font-display text-lg font-bold text-neutral-800">Belum Ada Pesanan</h3>
                    <p class="font-body text-xs text-neutral-500 max-w-sm mx-auto">
                        Anda belum memiliki riwayat pesanan. Mulai jelajahi katalog kriya tradisi pilihan kami.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-semibold shadow-sm transition">
                        Buka Katalog Kriya
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4" data-aos="fade-up">
                @foreach($orders as $ord)
                    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-5 hover:border-neutral-300 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-100">
                            <div>
                                <span class="font-mono font-bold text-sm text-primary-700">
                                    {{ $ord->order_number }}
                                </span>
                                <span class="text-xs text-neutral-400 ml-2">
                                    • {{ $ord->created_at->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            </div>
                            <div>
                                <x-status-badge :status="$ord->status" />
                            </div>
                        </div>

                        <div class="py-3 space-y-2">
                            @foreach($ord->orderItems as $item)
                                <div class="flex items-center justify-between text-xs font-body text-neutral-700">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold">{{ $item->product_name }}</span>
                                        @if($item->variant_label)
                                            <span class="text-neutral-500">({{ $item->variant_label }})</span>
                                        @endif
                                        <span class="text-neutral-400">× {{ $item->quantity }}</span>
                                    </div>
                                    <span class="font-medium">
                                        Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-neutral-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs font-body text-neutral-500">
                                Total Pembayaran: <span class="font-bold text-sm text-primary-700">Rp{{ number_format((float) $ord->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('order.confirmation', $ord->order_number) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-canvas-sunken hover:bg-neutral-200 text-neutral-800 font-body text-xs font-semibold transition">
                                    <span>Lihat Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-2">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.public>

