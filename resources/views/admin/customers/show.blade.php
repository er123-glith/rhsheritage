<x-layouts.admin>
    <x-slot:title>Profil Pelanggan — {{ $customer->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('admin.customers.index') }}" class="hover:text-primary-700 transition">Data Pelanggan</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-700 font-medium">{{ $customer->name }}</span>
            </nav>

            <div>
                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar Pelanggan</span>
                </a>
            </div>
        </div>

        <!-- Customer Identity Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-primary-700 text-gold-400 font-display font-bold text-2xl flex items-center justify-center shrink-0 border-2 border-gold-500 shadow-sm">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="font-display text-2xl font-bold text-neutral-900">{{ $customer->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                            Role: Customer
                        </span>
                    </div>
                    <p class="font-body text-sm text-neutral-500 mt-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $customer->email }}</span>
                    </p>
                    <p class="font-body text-xs text-neutral-400 mt-0.5">
                        Bergabung sejak {{ $customer->created_at->translatedFormat('d F Y, H:i') }} WIB ({{ $customer->created_at->diffForHumans() }})
                    </p>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1">
                <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold">Total Pesanan</p>
                <p class="font-body text-2xl font-bold text-primary-700 mt-1">{{ $customer->orders_count }}</p>
                <p class="font-body text-xs text-neutral-400 mt-1">Seluruh riwayat pesanan</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1">
                <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold">Menunggu Konfirmasi</p>
                <p class="font-body text-2xl font-bold text-gold-700 mt-1">{{ $pendingOrdersCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-1">Perlu ditindaklanjuti</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1">
                <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold">Pesanan Selesai</p>
                <p class="font-body text-2xl font-bold text-emerald-700 mt-1">{{ $completedOrdersCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-1">Transaksi sukses</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1">
                <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold">Total Nilai Belanja</p>
                <p class="font-body text-2xl font-bold text-primary-700 mt-1">Rp{{ number_format($totalSpent, 0, ',', '.') }}</p>
                <p class="font-body text-xs text-neutral-400 mt-1">Akumulasi pesanan selesai</p>
            </div>
        </div>

        <!-- Order History Table -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <div>
                    <h2 class="font-display text-base font-bold text-primary-700">Riwayat Pesanan Pelanggan</h2>
                    <p class="font-body text-xs text-neutral-400 mt-0.5">Daftar transaksi yang dibuat oleh akun ini</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-600">
                    {{ $orders->total() }} Transaksi
                </span>
            </div>

            @if($orders->isEmpty())
                <div class="p-10 text-center space-y-2">
                    <p class="font-body text-sm font-semibold text-neutral-700">Belum ada pesanan yang dibuat oleh pelanggan ini.</p>
                    <p class="font-body text-xs text-neutral-400">Pesanan baru yang dibuat oleh akun ini akan muncul di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-canvas-sunken border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">No. Pesanan & Tanggal</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Tujuan Pengiriman</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Biaya</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-body text-sm">
                            @foreach($orders as $order)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-bold text-primary-700 hover:text-primary-900 transition">
                                            #{{ $order->order_number }}
                                        </a>
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
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
                                            {{ $order->items->sum('quantity') }} item
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 hover:border-primary-600 transition shadow-xs">
                                            <span>Kelola Pesanan</span>
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

                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.admin>
