<x-layouts.admin>
    <x-slot:title>Manajemen Retur & Komplain</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Manajemen Retur</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Daftar Pengajuan Retur</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Kelola klaim retur barang rusak/cacat atau pesanan tidak sesuai, validasi video unboxing, proses penggantian barang J&T Express atau pengembalian dana.
                </p>
            </div>
            @if(($counts['menunggu_review'] ?? 0) > 0)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gold-100/90 border border-gold-300 text-gold-900 font-body text-xs font-bold shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500 animate-ping"></span>
                    <span>{{ $counts['menunggu_review'] }} Pengajuan Perlu Ditinjau</span>
                </div>
            @endif
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-sm border-b border-neutral-200">
            <a href="{{ route('admin.returns.index', array_filter(['solution' => request('solution'), 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ !request('status') ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Semua</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">{{ $counts['all'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_filter(['status' => 'menunggu_review', 'solution' => request('solution'), 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'menunggu_review' ? 'border-gold-500 text-gold-800 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-gold-700 hover:border-gold-300' }}">
                <span>Menunggu Review</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gold-100 text-gold-800">{{ $counts['menunggu_review'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_filter(['status' => 'dalam_penyelesaian', 'solution' => request('solution'), 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'dalam_penyelesaian' ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Dalam Penyelesaian</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-100 text-primary-800">{{ $counts['dalam_penyelesaian'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_filter(['status' => 'selesai', 'solution' => request('solution'), 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'selesai' ? 'border-success text-success bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Selesai</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">{{ $counts['selesai'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.returns.index', array_filter(['status' => 'ditolak', 'solution' => request('solution'), 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'ditolak' ? 'border-danger text-danger bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Ditolak</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $counts['ditolak'] ?? 0 }}</span>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('admin.returns.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative sm:col-span-2">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari no. retur, no. pesanan, nama customer, atau no. HP..."
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex gap-2">
                <select name="solution" class="w-full h-11 px-3 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 text-neutral-700">
                    <option value="">Semua Solusi</option>
                    <option value="replacement" {{ request('solution') === 'replacement' ? 'selected' : '' }}>Penggantian Barang</option>
                    <option value="refund" {{ request('solution') === 'refund' ? 'selected' : '' }}>Pengembalian Dana</option>
                </select>

                <x-button type="submit" variant="primary" size="md" class="shrink-0">
                    Filter
                </x-button>

                @if(request('q') || request('solution') || request('status'))
                    <a href="{{ route('admin.returns.index') }}" class="px-3.5 py-2 rounded-lg border border-neutral-200 hover:bg-neutral-50 text-neutral-600 font-body text-sm flex items-center justify-center transition shrink-0" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Table Container -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            @if($returns->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-neutral-800">Tidak ada pengajuan retur</h3>
                    <p class="font-body text-sm text-neutral-500 mt-1 max-w-md mx-auto">
                        Belum ada klaim retur atau komplain yang sesuai dengan kriteria filter saat ini.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-body text-sm divide-y divide-neutral-200">
                        <thead class="bg-neutral-50 text-xs font-semibold uppercase tracking-wider text-neutral-500">
                            <tr>
                                <th class="px-5 py-3.5">No. Retur & Tanggal</th>
                                <th class="px-5 py-3.5">Pesanan & Pelanggan</th>
                                <th class="px-5 py-3.5">Solusi & Alasan</th>
                                <th class="px-5 py-3.5 text-center">Item Retur</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($returns as $return)
                                <tr class="hover:bg-neutral-50/70 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-mono text-sm font-bold text-primary-700">
                                            {{ $return->return_number }}
                                        </div>
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            {{ $return->created_at->format('d M Y, H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-mono text-xs font-semibold text-neutral-800">
                                            <a href="{{ route('admin.orders.show', $return->order_id) }}" class="hover:text-primary-700 hover:underline">
                                                {{ $return->order->order_number }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-neutral-600 font-medium mt-0.5">
                                            {{ $return->customer->name ?? $return->order->customer_name }}
                                        </div>
                                        <div class="text-xs text-neutral-400">
                                            {{ $return->customer->phone ?? $return->order->customer_phone }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-bold {{ $return->solution === 'replacement' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            @if($return->solution === 'replacement')
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                <span>Ganti Barang</span>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Refund Dana</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-neutral-600 mt-1 line-clamp-1" title="{{ $return->reason }}">
                                            {{ $return->reason }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-700">
                                            {{ $return->items->sum('quantity') }} unit ({{ $return->items->count() }} item)
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $return->status->badgeClass() }}">
                                            {{ $return->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.returns.show', $return) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg font-body text-xs font-semibold bg-primary-50 text-primary-700 hover:bg-primary-100 border border-primary-200 transition">
                                            <span>Periksa Detail</span>
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

                @if($returns->hasPages())
                    <div class="p-4 border-t border-neutral-200">
                        {{ $returns->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.admin>
