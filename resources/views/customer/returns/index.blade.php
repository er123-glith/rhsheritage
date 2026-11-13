<x-layouts.customer>
    <x-slot:title>Retur Saya — RHS HERITAGE</x-slot:title>

    <div class="space-y-6">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <nav class="flex items-center gap-2 font-body text-xs text-neutral-400 mb-1">
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Retur Saya</span>
                </nav>
                <h1 class="font-display text-2xl font-bold text-primary-700">Manajemen Retur Barang</h1>
                <p class="font-body text-xs text-neutral-500 mt-1">
                    Pantau proses penggantian barang (replacement) dan pengembalian dana (refund) untuk pesanan Anda.
                </p>
            </div>

            <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Pilih Pesanan untuk Diretur</span>
            </a>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 border-b border-neutral-200 overflow-x-auto pb-2 font-body text-xs">
            <a href="{{ route('customer.returns.index') }}" class="px-4 py-2 rounded-lg font-semibold transition {{ !request('status') ? 'bg-primary-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('customer.returns.index', ['status' => 'menunggu_review']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') === 'menunggu_review' ? 'bg-primary-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                Menunggu Review
            </a>
            <a href="{{ route('customer.returns.index', ['status' => 'dalam_penyelesaian']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') === 'dalam_penyelesaian' ? 'bg-primary-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                Dalam Penyelesaian
            </a>
            <a href="{{ route('customer.returns.index', ['status' => 'selesai']) }}" class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') === 'selesai' ? 'bg-primary-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                Selesai ({{ $completedCount }})
            </a>
        </div>

        <!-- Returns List -->
        @if($returns->isEmpty())
            <div class="bg-white rounded-xl border border-neutral-200 p-12 text-center shadow-1 font-body">
                <div class="w-16 h-16 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400 mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                </div>
                <h3 class="font-display text-lg font-bold text-neutral-800">Belum Ada Pengajuan Retur</h3>
                <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">
                    Jika produk yang Anda terima mengalami kerusakan atau tidak sesuai, Anda dapat mengajukan retur melalui halaman Detail Pesanan.
                </p>
                <div class="mt-5">
                    <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white text-xs font-semibold transition">
                        Buka Riwayat Pesanan Saya
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($returns as $return)
                    <div class="bg-white rounded-xl border border-neutral-200 p-5 shadow-1 font-body hover:border-primary-300 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-neutral-100 pb-3 mb-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-sm text-primary-900">{{ $return->return_number }}</span>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold border {{ $return->status->badgeClasses() }}">
                                    {{ $return->status->label() }}
                                </span>
                            </div>
                            <span class="text-xs text-neutral-400">
                                Diajukan pada {{ $return->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-neutral-400 uppercase tracking-wider text-[10px] block font-semibold">Nomor Pesanan</span>
                                <a href="{{ route('customer.orders.show', $return->order->order_number) }}" class="font-mono font-bold text-primary-700 hover:underline">
                                    #{{ $return->order->order_number }}
                                </a>
                            </div>
                            <div>
                                <span class="text-neutral-400 uppercase tracking-wider text-[10px] block font-semibold">Solusi Diminta</span>
                                <span class="font-semibold text-neutral-800">{{ $return->solutionLabel() }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-400 uppercase tracking-wider text-[10px] block font-semibold">Alasan Retur</span>
                                <span class="text-neutral-700">{{ $return->reasonLabel() }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between">
                            <span class="text-xs text-neutral-500 font-medium">
                                {{ $return->items->count() }} jenis produk diretur
                            </span>
                            <a href="{{ route('customer.returns.show', $return->return_number) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-800 hover:bg-primary-100 text-xs font-semibold transition">
                                <span>Lihat Detail & Lacak Status</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4">
                    {{ $returns->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.customer>

