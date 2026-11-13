<x-layouts.admin>
    <x-slot:title>Pesanan Masuk</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Pesanan Masuk</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Daftar Pesanan Masuk</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Kelola transaksi masuk, konfirmasi ketersediaan stok, perbarui status pengiriman dan nomor resi ekspedisi.
                </p>
            </div>
            @if($pendingCount > 0)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gold-100/90 border border-gold-300 text-gold-900 font-body text-xs font-bold shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500 animate-ping"></span>
                    <span>{{ $pendingCount }} Pesanan Baru Menunggu Konfirmasi</span>
                </div>
            @endif
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-sm border-b border-neutral-200">
            <a href="{{ route('admin.orders.index') }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ !request('status') ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Semua</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">{{ $totalOrders }}</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_konfirmasi']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'menunggu_konfirmasi' ? 'border-gold-500 text-gold-800 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-gold-700 hover:border-gold-300' }}">
                <span>Menunggu Konfirmasi</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gold-100 text-gold-800">{{ $pendingCount }}</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'diproses']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'diproses' ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Diproses</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-800">{{ $processingCount }}</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'dikirim']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'dikirim' ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Dikirim</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-800">{{ $shippedCount }}</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'selesai']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'selesai' ? 'border-success text-success bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Selesai</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">{{ $completedCount }}</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'dibatalkan']) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'dibatalkan' ? 'border-danger text-danger bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Dibatalkan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $cancelledCount }}</span>
            </a>
        </div>

        <!-- Search & Date Filter Bar -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative lg:col-span-2">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari no. pesanan, nama pembeli, no. HP, atau kota..."
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div>
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    title="Tanggal Dari"
                    class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 text-neutral-700"
                />
            </div>

            <div class="flex gap-2">
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    title="Tanggal Sampai"
                    class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 text-neutral-700"
                />
                <x-button type="submit" variant="secondary" class="h-11 px-5">Filter</x-button>
                @if(request()->hasAny(['q', 'date_from', 'date_to', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="h-11 px-3.5 inline-flex items-center justify-center rounded-lg font-body text-xs border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Orders Table Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-canvas-sunken border-b border-neutral-200">
                        <tr>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">No. Pesanan & Tanggal</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Pelanggan</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Tujuan & Zona</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Biaya</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($orders as $order)
                            @php
                                $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                if (str_starts_with($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $waText = rawurlencode("Halo Kak {$order->customer_name}, kami dari RHS HERITAGE ingin mengonfirmasi pesanan #{$order->order_number}.");
                                $waUrl = "https://wa.me/{$phone}?text={$waText}";
                            @endphp
                            <tr class="hover:bg-canvas-sunken/40 transition">
                                <!-- Order Number & Date -->
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-sm font-bold text-primary-700 hover:text-primary-900 transition block">
                                        #{{ $order->order_number }}
                                    </a>
                                    <div class="font-body text-xs text-neutral-400 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>
                                </td>

                                <!-- Customer Info & WhatsApp quick chat -->
                                <td class="px-5 py-4 font-body text-sm">
                                    <div class="font-semibold text-neutral-800">{{ $order->customer_name }}</div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-xs text-neutral-500 font-mono">{{ $order->customer_phone }}</span>
                                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:text-emerald-700" title="Chat di WhatsApp">
                                            <svg class="w-3.5 h-3.5 fill-current inline" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @if($order->user)
                                        <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('admin.customers.show', $order->user) }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold bg-primary-50 text-primary-700 hover:bg-primary-100 transition border border-primary-200">
                                                <span>Member</span>
                                                <span class="text-[9px]">↗</span>
                                            </a>
                                            <span class="text-[11px] text-neutral-400 font-mono truncate max-w-[140px]">{{ $order->user->email }}</span>
                                        </div>
                                    @else
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium bg-neutral-100 text-neutral-500">
                                                Tamu / Pesanan Lama
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Destination & Shipping Zone -->
                                <td class="px-5 py-4 font-body text-sm">
                                    <div class="font-medium text-neutral-800">{{ $order->city }}, {{ $order->province }}</div>
                                    <div class="text-xs text-neutral-500 mt-0.5">
                                        {{ $order->shipping_zone_name ?? ($order->shippingZone->name ?? 'Reguler') }}
                                    </div>
                                </td>

                                <!-- Total Financial & Items -->
                                <td class="px-5 py-4 font-body text-sm">
                                    <div class="font-bold text-neutral-900">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-neutral-500 mt-0.5">
                                        {{ $order->items->sum('quantity') }} item • {{ number_format($order->total_weight_gram) }} gr
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$order->status" />
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($order->status === \App\Enums\OrderStatus::MenungguKonfirmasi)
                                            <form method="POST" action="{{ route('admin.orders.confirm', $order) }}" data-confirm="Konfirmasi pesanan #{{ $order->order_number }}? Stok varian akan otomatis dipotong.">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-gold-500 hover:bg-gold-600 text-neutral-900 font-body text-xs font-bold transition shadow-xs">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mx-auto mb-4 text-neutral-400">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <p class="font-display font-bold text-neutral-800 text-base">Belum Ada Pesanan</p>
                                        <p class="font-body text-xs text-neutral-500 mt-1">Tidak ada pesanan yang sesuai dengan filter atau kriteria pencarian saat ini.</p>
                                        @if(request()->hasAny(['q', 'date_from', 'date_to', 'status']))
                                            <a href="{{ route('admin.orders.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 text-white font-body text-xs font-semibold hover:bg-primary-800 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span>Tampilkan Semua Pesanan</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-5 py-4 border-t border-neutral-200 bg-canvas-sunken/40">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
