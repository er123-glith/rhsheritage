<x-layouts.admin>
    <x-slot:title>Manajemen Pelanggan</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Data Pelanggan</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Daftar Pelanggan Terdaftar</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Kelola akun customer yang mendaftar melalui website, pantau total riwayat pesanan, dan akumulasi transaksi belanja.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-primary-50 border border-primary-200 text-primary-800 font-body text-xs font-bold shadow-xs">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Total: {{ $totalCustomers }} Akun Pelanggan</span>
            </div>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari pelanggan berdasarkan nama atau alamat email..."
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
                @if(request('q'))
                    <a href="{{ route('admin.customers.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition" title="Reset pencarian">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Customers Table -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            @if($customers->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 text-neutral-400 mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-bold text-neutral-800">Tidak Ada Data Pelanggan</h3>
                        <p class="font-body text-xs sm:text-sm text-neutral-500 max-w-sm mx-auto mt-1">
                            @if(request('q'))
                                Tidak ditemukan pelanggan dengan kata kunci "{{ request('q') }}".
                            @else
                                Belum ada pelanggan yang mendaftar pada website.
                            @endif
                        </p>
                    </div>
                    @if(request('q'))
                        <div class="pt-2">
                            <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                                Reset Pencarian
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-canvas-sunken border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Pelanggan</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Tanggal Bergabung</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center">Total Pesanan</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Akumulasi Belanja</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-body text-sm">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-semibold text-neutral-900 hover:text-primary-700 transition block truncate">
                                                    {{ $customer->name }}
                                                </a>
                                                <p class="text-xs text-neutral-400 truncate">{{ $customer->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-600">
                                        <div class="font-medium text-neutral-800">
                                            {{ $customer->created_at->translatedFormat('d F Y') }}
                                        </div>
                                        <div class="text-neutral-400 mt-0.5">
                                            {{ $customer->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $customer->orders_count > 0 ? 'bg-primary-50 text-primary-700 border border-primary-200' : 'bg-neutral-100 text-neutral-500' }}">
                                            {{ $customer->orders_count }} Pesanan
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-neutral-900">
                                            Rp{{ number_format((float) ($customer->total_spent ?? 0), 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-neutral-400 mt-0.5">
                                            Transaksi Selesai
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 hover:border-primary-600 transition shadow-xs">
                                            <span>Detail Profil</span>
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

                @if($customers->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200">
                        {{ $customers->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.admin>
