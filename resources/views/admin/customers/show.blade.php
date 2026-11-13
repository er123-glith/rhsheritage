<x-layouts.admin>
    <x-slot:title>Profil Pelanggan — {{ $customer->name }}</x-slot:title>

    <div class="space-y-6" x-data="{
        showDeleteModal: false,
        confirmInput: '',
        openDelete() {
            this.confirmInput = '';
            this.showDeleteModal = true;
        }
    }">
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

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white text-xs font-semibold transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit Profil</span>
                </a>

                @if(! $customer->trashed())
                    <button type="button" @click="openDelete()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 text-xs font-semibold transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Nonaktifkan Akun</span>
                    </button>
                @endif

                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
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

        <!-- Customer Identity Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full {{ $customer->trashed() ? 'bg-neutral-200 text-neutral-500' : 'bg-primary-700 text-gold-400' }} font-display font-bold text-2xl flex items-center justify-center shrink-0 border-2 border-gold-500 shadow-sm">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="font-display text-2xl font-bold text-neutral-900">{{ $customer->name }}</h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-neutral-100 text-neutral-700 border border-neutral-200">
                            ID: #{{ $customer->id }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                            Role: Customer
                        </span>
                        @if($customer->trashed())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                Akun Nonaktif (Soft Deleted)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Akun Aktif
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6 text-sm text-neutral-500 mt-2 font-body">
                        <p class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $customer->email }}</span>
                        </p>
                        <p class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ $customer->phone ?? 'Belum ada nomor telepon' }}</span>
                        </p>
                    </div>
                    <p class="font-body text-xs text-neutral-400 mt-1">
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
                    <p class="font-body text-xs text-neutral-400 mt-0.5">Daftar transaksi yang terhubung dengan akun pelanggan ini</p>
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
                    <table class="w-full text-left font-body text-sm">
                        <thead class="bg-canvas-sunken border-b border-neutral-200">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500">No. Pesanan</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500">Tanggal</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500">Item Produk</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500">Total Transaksi</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($orders as $order)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-primary-700">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-500">
                                        {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="px-6 py-4 text-xs text-neutral-700">
                                        {{ $order->items->count() }} item produk
                                    </td>
                                    <td class="px-6 py-4 font-bold text-neutral-900">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-xs">
                                            <span>Detail Pesanan</span>
                                            <span>→</span>
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
                        <h3 class="font-display text-lg font-bold text-neutral-900">Nonaktifkan Akun Pelanggan</h3>
                        <p class="font-body text-xs text-neutral-500">Tindakan ini memerlukan verifikasi ganda.</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-lg bg-canvas-sunken border border-neutral-200 text-xs font-body space-y-1">
                    <p class="font-semibold text-neutral-800">Pelanggan: {{ $customer->name }} (#{{ $customer->id }})</p>
                    <p class="text-neutral-500">Email: {{ $customer->email }}</p>
                </div>

                <div class="text-xs text-neutral-600 font-body leading-relaxed space-y-2">
                    <p>Akun pelanggan akan dinonaktifkan (soft delete). Pelanggan tidak akan dapat login kembali, namun <strong>seluruh riwayat transaksi belanja akan tetap dipertahankan</strong> untuk laporan akuntansi toko.</p>
                    <p class="font-semibold text-neutral-800">Ketik kata <span class="font-mono text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-200">HAPUS</span> di bawah ini untuk melanjutkan:</p>
                </div>

                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="space-y-4">
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
