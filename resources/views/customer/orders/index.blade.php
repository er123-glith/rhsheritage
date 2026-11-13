<x-layouts.customer>
    <x-slot:title>Pesanan Saya</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Pesanan Saya</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Riwayat Pesanan Saya</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Lihat status proses, nomor resi pengiriman, dan rincian seluruh pesanan yang pernah Anda lakukan.
                </p>
            </div>
            <div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold transition shadow-xs">
                    <span>Belanja Produk Lain</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-sm border-b border-neutral-200">
            <a href="{{ route('customer.orders') }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ !request('status') ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Semua</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">{{ $totalOrders }}</span>
            </a>

            <a href="{{ route('customer.orders', ['status' => 'menunggu_konfirmasi']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'menunggu_konfirmasi' ? 'border-gold-500 text-gold-800 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-gold-700 hover:border-gold-300' }}">
                <span>Menunggu Konfirmasi</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gold-100 text-gold-800">{{ $pendingCount }}</span>
            </a>

            <a href="{{ route('customer.orders', ['status' => 'diproses']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'diproses' ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Diproses</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-800">{{ $processingCount }}</span>
            </a>

            <a href="{{ route('customer.orders', ['status' => 'dikirim']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'dikirim' ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Dikirim</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-800">{{ $shippedCount }}</span>
            </a>

            <a href="{{ route('customer.orders', ['status' => 'selesai']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'selesai' ? 'border-success text-success bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Selesai</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">{{ $completedCount }}</span>
            </a>

            <a href="{{ route('customer.orders', ['status' => 'dibatalkan']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'dibatalkan' ? 'border-danger text-danger bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Dibatalkan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $cancelledCount }}</span>
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('customer.orders') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 flex flex-col sm:flex-row gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari berdasarkan nomor pesanan (contoh: RHS-2026...)"
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="h-11 px-6 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-semibold transition">
                    Cari
                </button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('customer.orders') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Orders Table / List -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            @if($orders->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-primary-50 text-primary-700 mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-bold text-neutral-800">Tidak Ada Pesanan Ditemukan</h3>
                        <p class="font-body text-xs sm:text-sm text-neutral-500 max-w-sm mx-auto mt-1">
                            @if(request()->hasAny(['q', 'status']))
                                Tidak ada pesanan yang sesuai dengan filter atau kata kunci pencarian Anda.
                            @else
                                Anda belum memiliki riwayat pesanan. Mulai berbelanja kriya pilihan sekarang!
                            @endif
                        </p>
                    </div>
                    @if(request()->hasAny(['q', 'status']))
                        <div class="pt-2">
                            <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                                Hapus Filter
                            </a>
                        </div>
                    @else
                        <div class="pt-2">
                            <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-semibold transition shadow-xs">
                                Jelajahi Katalog
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-canvas-sunken border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">No. Pesanan & Tanggal</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Produk yang Dipesan</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Alamat Tujuan</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Biaya</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-body text-sm">
                            @foreach($orders as $order)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('customer.orders.show', $order->order_number) }}" class="font-mono font-bold text-primary-700 hover:text-primary-900 transition">
                                            #{{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-neutral-400 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @foreach($order->orderItems->take(2) as $item)
                                                <div class="text-xs text-neutral-700">
                                                    <span class="font-semibold">{{ $item->product_name }}</span>
                                                    @if($item->variant_label)
                                                        <span class="text-neutral-500">({{ $item->variant_label }})</span>
                                                    @endif
                                                    <span class="text-neutral-400">× {{ $item->quantity }}</span>
                                                </div>
                                            @endforeach
                                            @if($order->orderItems->count() > 2)
                                                <p class="text-[11px] text-neutral-400 italic">
                                                    +{{ $order->orderItems->count() - 2 }} item lainnya
                                                </p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-600">
                                        <div class="font-medium text-neutral-800">{{ $order->city }}</div>
                                        <div class="text-neutral-400 mt-0.5">{{ $order->province }}</div>
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
                                        <div class="flex items-center justify-end gap-2">
                                            @if($order->status === \App\Enums\OrderStatus::Selesai)
                                                @if($order->review)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[11px] font-semibold" title="Sudah Diulas">
                                                        <i class="fa-solid fa-star text-gold-500 text-[10px]"></i>
                                                        <span>{{ $order->review->rating }}★</span>
                                                    </span>
                                                @else
                                                    <a href="{{ route('customer.reviews.create', $order->order_number) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 text-xs font-bold transition shadow-xs">
                                                        <i class="fa-solid fa-star text-[10px]"></i>
                                                        <span>Beri Ulasan</span>
                                                    </a>
                                                @endif
                                            @endif
                                            <a href="{{ route('customer.orders.show', $order->order_number) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 dark:border-neutral-700 text-xs font-semibold text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800 hover:border-primary-600 transition shadow-xs">
                                                <span>Rincian</span>
                                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.customer>

