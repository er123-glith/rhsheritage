<x-layouts.customer>
    <x-slot:title>Dashboard Pelanggan</x-slot:title>

    <div class="space-y-6">
        <!-- Welcome Hero Banner -->
        <div class="rounded-xl bg-gradient-to-r from-primary-700 to-primary-800 text-white p-6 sm:p-8 shadow-2 relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-sm bg-gold-500 text-neutral-900 font-body text-xs font-bold uppercase tracking-wider mb-3">
                    Pelanggan RHS Heritage
                </span>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-white mb-2">
                    Selamat Datang, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="font-body text-sm text-primary-100 leading-relaxed">
                    Pantau status pesanan songkok, sarung ATBM, dan jilbab sekolah Anda secara real-time. Anda juga dapat melanjutkan belanja produk kriya pilihan langsung dari panel akun Anda.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gold-500 hover:bg-gold-600 text-neutral-900 font-body text-xs font-bold transition shadow-xs">
                        <span>Jelajahi Katalog Kriya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-500 text-white font-body text-xs font-semibold transition border border-primary-500">
                        <span>Buka Keranjang Belanja</span>
                    </a>
                </div>
            </div>
            <!-- Decorative Accent -->
            <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full border-8 border-gold-500/20 pointer-events-none"></div>
        </div>

        <!-- Summary KPI Cards (Live DB data) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Orders -->
            <a href="{{ route('customer.orders') }}" class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-primary-400 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Total Pesanan</p>
                    <p class="font-body text-2xl font-bold text-primary-700 mt-1">{{ $totalOrders }}</p>
                    <p class="font-body text-[11px] text-neutral-400 mt-1">Semua transaksi →</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-50 text-primary-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </a>

            <!-- Pending Orders -->
            <a href="{{ route('customer.orders', ['status' => 'menunggu_konfirmasi']) }}" class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-gold-400 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-gold-700 transition">Menunggu</p>
                    <p class="font-body text-2xl font-bold text-gold-700 mt-1">{{ $pendingOrders }}</p>
                    <p class="font-body text-[11px] text-neutral-400 mt-1">Verifikasi owner →</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- Processing / In Transit Orders -->
            <a href="{{ route('customer.orders', ['status' => 'diproses']) }}" class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-primary-400 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Diproses</p>
                    <p class="font-body text-2xl font-bold text-primary-700 mt-1">{{ $processingOrders }}</p>
                    <p class="font-body text-[11px] text-neutral-400 mt-1">Disiapkan / jalan →</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
            </a>

            <!-- Completed Orders -->
            <a href="{{ route('customer.orders', ['status' => 'selesai']) }}" class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-emerald-500 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-emerald-700 transition">Pesanan Selesai</p>
                    <p class="font-body text-2xl font-bold text-emerald-700 mt-1">{{ $completedOrders }}</p>
                    <p class="font-body text-[11px] text-neutral-400 mt-1">Pesanan sukses →</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- Total Spent -->
            <div class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold">Total Belanja</p>
                    <p class="font-body text-xl font-bold text-neutral-900 mt-1">Rp{{ number_format($totalSpent, 0, ',', '.') }}</p>
                    <p class="font-body text-[11px] text-neutral-400 mt-1">Pesanan selesai</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gold-50 text-gold-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <div>
                    <h2 class="font-display text-base sm:text-lg font-bold text-primary-700">Pesanan Terbaru</h2>
                    <p class="font-body text-xs text-neutral-400 mt-0.5">Daftar 5 pesanan terakhir yang Anda buat</p>
                </div>
                <a href="{{ route('customer.orders') }}" class="font-body text-xs font-semibold text-primary-700 hover:text-primary-900 transition flex items-center gap-1">
                    <span>Lihat Semua Pesanan</span>
                    <span>→</span>
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="p-10 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-primary-50 text-primary-700 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-base font-bold text-neutral-800">Belum Ada Pesanan</h3>
                        <p class="font-body text-xs text-neutral-500 max-w-sm mx-auto mt-1">
                            Anda belum pernah melakukan pemesanan. Jelajahi katalog kriya terbaik dari RHS Heritage sekarang.
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold transition shadow-xs">
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-canvas-sunken border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">No. Pesanan & Waktu</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Item Produk</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Biaya</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-body text-sm">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('customer.orders.show', $order->order_number) }}" class="font-mono font-bold text-primary-700 hover:text-primary-900 transition">
                                            #{{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-600">
                                        <div class="font-medium text-neutral-800">
                                            {{ $order->orderItems->first()?->product_name ?? 'Item Produk' }}
                                        </div>
                                        @if($order->orderItems->count() > 1)
                                            <div class="text-[11px] text-neutral-400 mt-0.5">
                                                +{{ $order->orderItems->count() - 1 }} produk lainnya
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-neutral-900">
                                            Rp{{ number_format($order->total, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            {{ $order->orderItems->sum('quantity') }} pcs
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('customer.orders.show', $order->order_number) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 hover:border-primary-600 transition">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.customer>

