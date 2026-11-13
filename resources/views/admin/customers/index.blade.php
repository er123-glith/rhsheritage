<x-layouts.admin>
    <x-slot:title>Manajemen Pelanggan</x-slot:title>

    <div class="space-y-6" x-data="{
        showDeleteModal: false,
        deleteUrl: '',
        customerName: '',
        customerEmail: '',
        confirmInput: '',
        openDelete(url, name, email) {
            this.deleteUrl = url;
            this.customerName = name;
            this.customerEmail = email;
            this.confirmInput = '';
            this.showDeleteModal = true;
        }
    }">
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
                    Kelola akun customer, pantau riwayat belanja, dan administrasi status akun secara aman.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-primary-50 border border-primary-200 text-primary-800 font-body text-xs font-bold shadow-xs">
                <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Total: {{ $totalCustomers }} Pelanggan</span>
            </div>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 font-body text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Status & Search Bar -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 space-y-4">
            <!-- Tabs Status Akun -->
            <div class="flex items-center gap-2 border-b border-neutral-200 pb-3 overflow-x-auto">
                <a href="{{ route('admin.customers.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'all' ? 'bg-primary-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                    Semua ({{ $totalCustomers }})
                </a>
                <a href="{{ route('admin.customers.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'active' ? 'bg-emerald-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                    Aktif ({{ $activeCustomersCount }})
                </a>
                <a href="{{ route('admin.customers.index', array_merge(request()->except(['status', 'page']), ['status' => 'inactive'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $statusFilter === 'inactive' || $statusFilter === 'trashed' ? 'bg-red-700 text-white shadow-xs' : 'text-neutral-600 hover:bg-neutral-100' }}">
                    Nonaktif / Dihapus ({{ $inactiveCustomersCount }})
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="status" value="{{ $statusFilter }}" />
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari pelanggan berdasarkan nama, email, atau no. telepon..."
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
                    @if(request('q') || request('status'))
                        <a href="{{ route('admin.customers.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

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
                                Belum ada pelanggan yang terdaftar pada kategori ini.
                            @endif
                        </p>
                    </div>
                    @if(request('q') || request('status'))
                        <div class="pt-2">
                            <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                                Reset Pencarian & Filter
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
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Kontak</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center">Pesanan</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Belanja</th>
                                <th class="px-6 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 font-body text-sm">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full {{ $customer->trashed() ? 'bg-neutral-200 text-neutral-500' : 'bg-primary-100 text-primary-700' }} font-bold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-semibold text-neutral-900 hover:text-primary-700 transition block truncate">
                                                    {{ $customer->name }}
                                                </a>
                                                <p class="text-xs text-neutral-400 truncate">{{ $customer->email }}</p>
                                                <span class="text-[10px] text-neutral-400">ID: #{{ $customer->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-600">
                                        @if($customer->phone)
                                            <span class="font-mono text-neutral-800">{{ $customer->phone }}</span>
                                        @else
                                            <span class="text-neutral-400 italic">Belum diisi</span>
                                        @endif
                                        <div class="text-[11px] text-neutral-400 mt-0.5">
                                            Daftar: {{ $customer->created_at->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($customer->trashed())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                Nonaktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @endif
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
                                        <div class="text-[11px] text-neutral-400 mt-0.5">
                                            Transaksi Selesai
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Tombol Detail -->
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="p-1.5 rounded-lg border border-neutral-300 text-neutral-700 hover:bg-neutral-50 transition shadow-xs" title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="p-1.5 rounded-lg border border-neutral-300 text-neutral-700 hover:bg-neutral-50 hover:border-primary-600 hover:text-primary-700 transition shadow-xs" title="Edit Pelanggan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <!-- Tombol Hapus (Soft Delete) -->
                                            @if(! $customer->trashed())
                                                <button type="button"
                                                        @click="openDelete('{{ route('admin.customers.destroy', $customer) }}', '{{ addslashes($customer->name) }}', '{{ addslashes($customer->email) }}')"
                                                        class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition shadow-xs"
                                                        title="Nonaktifkan Akun">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
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

        <!-- Modal Konfirmasi Soft Delete -->
        <div x-show="showDeleteModal"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
             @keydown.escape.window="showDeleteModal = false">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2 border border-neutral-200"
                 @click.away="showDeleteModal = false">
                <div class="flex items-center gap-3 text-red-600">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-bold text-neutral-900">Konfirmasi Penonaktifan Akun</h3>
                        <p class="font-body text-xs text-neutral-500">Tindakan ini memerlukan verifikasi ganda.</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-lg bg-canvas-sunken border border-neutral-200 text-xs font-body space-y-1">
                    <p class="font-semibold text-neutral-800" x-text="'Pelanggan: ' + customerName"></p>
                    <p class="text-neutral-500" x-text="'Email: ' + customerEmail"></p>
                </div>

                <div class="text-xs text-neutral-600 font-body leading-relaxed space-y-2">
                    <p>Akun pelanggan akan <strong>dinonaktifkan (soft delete)</strong>. Pengguna tidak akan dapat login kembali, namun <strong>seluruh riwayat pesanan dan bukti transaksi akan tetap tersimpan aman</strong> untuk keperluan pembukuan.</p>
                    <p class="font-semibold text-neutral-800">Ketik kata <span class="font-mono text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-200">HAPUS</span> di bawah ini untuk melanjutkan:</p>
                </div>

                <form :action="deleteUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <input type="text"
                           name="confirmation"
                           x-model="confirmInput"
                           placeholder="Ketik HAPUS di sini"
                           required
                           autocomplete="off"
                           class="w-full h-11 px-4 rounded-lg font-mono text-sm border border-neutral-300 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-200 transition" />

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="px-4 py-2.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="confirmInput !== 'HAPUS'"
                                :class="confirmInput === 'HAPUS' ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-neutral-300 cursor-not-allowed text-neutral-500'"
                                class="px-5 py-2.5 rounded-lg text-white font-body text-xs font-semibold transition shadow-xs">
                            Nonaktifkan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
