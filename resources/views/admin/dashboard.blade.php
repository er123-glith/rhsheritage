<x-layouts.admin>
    <x-slot:title>Ringkasan Dashboard</x-slot:title>

    <div class="space-y-6">
        <!-- Welcome Hero Banner -->
        <div class="rounded-xl bg-gradient-to-r from-primary-700 to-primary-800 text-white p-6 sm:p-8 shadow-2 relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <span class="inline-block px-3 py-1 rounded-sm bg-gold-500 text-neutral-900 font-body text-xs font-bold uppercase tracking-wider mb-3">
                    Panel Owner Aktif
                </span>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-white mb-2">
                    Selamat Datang di Panel RHS HERITAGE
                </h1>
                <p class="font-body text-sm text-primary-100 leading-relaxed">
                    Sistem informasi e-commerce kriya (songkok, sarung ATBM, dan jilbab sekolah). Kelola stok produk, konfirmasi pesanan masuk, tindak lanjuti kemitraan grosir, serta atur zona tarif ongkir.
                </p>
            </div>
            <!-- Decorative Motif Accent -->
            <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full border-8 border-gold-500/20 pointer-events-none"></div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <!-- Pending Orders -->
            <a href="{{ route('admin.orders.index', ['status' => 'menunggu_konfirmasi']) }}" 
               class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-gold-400 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-gold-700 transition">Pesanan Baru</p>
                    <p class="font-body text-3xl font-bold text-primary-700 mt-1">{{ $pendingOrdersCount }}</p>
                    <p class="font-body text-xs text-neutral-400 mt-1">Menunggu konfirmasi owner →</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-gold-100 text-gold-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </a>

            <!-- Pending Partner Applications -->
            <a href="{{ route('admin.partners.index', ['status' => 'pending']) }}" 
               class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-primary-400 hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Pengajuan Mitra</p>
                    <p class="font-body text-3xl font-bold text-primary-700 mt-1">{{ $pendingPartnersCount }}</p>
                    <p class="font-body text-xs text-neutral-400 mt-1">Grosir & toko mitra baru →</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </a>

            <!-- Low Stock Alert -->
            <a href="{{ route('admin.stock.index', ['stock_status' => 'low_stock']) }}" 
               class="bg-white rounded-xl p-5 border border-neutral-200 shadow-1 flex items-center justify-between hover:border-warning hover:shadow-2 transition group">
                <div>
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-warning transition">Stok Menipis</p>
                    <p class="font-body text-3xl font-bold text-primary-700 mt-1">{{ $lowStockCount }}</p>
                    <p class="font-body text-xs text-neutral-400 mt-1">Varian di bawah ambang batas →</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-warning-bg text-warning flex items-center justify-center font-bold group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- Quick Access Modules Grid -->
        <div class="bg-white rounded-xl p-6 border border-neutral-200 shadow-1">
            <h2 class="font-display text-lg font-bold text-primary-700 mb-4">Akses Cepat Pengelolaan Toko</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.stock.index') }}" class="p-4 rounded-lg bg-canvas-sunken border border-neutral-200 hover:border-primary-400 hover:bg-white transition block">
                    <div class="flex items-center gap-2 text-primary-700 font-semibold font-body text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Kelola Stok</span>
                    </div>
                    <p class="font-body text-xs text-neutral-500">Update kuantitas stok varian, cek stok menipis dan habis.</p>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="p-4 rounded-lg bg-canvas-sunken border border-neutral-200 hover:border-primary-400 hover:bg-white transition block">
                    <div class="flex items-center gap-2 text-primary-700 font-semibold font-body text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Pesanan Masuk</span>
                    </div>
                    <p class="font-body text-xs text-neutral-500">Konfirmasi pesanan, input nomor resi pengiriman, dan riwayat pesanan.</p>
                </a>

                <a href="{{ route('admin.partners.index') }}" class="p-4 rounded-lg bg-canvas-sunken border border-neutral-200 hover:border-primary-400 hover:bg-white transition block">
                    <div class="flex items-center gap-2 text-primary-700 font-semibold font-body text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Grosir & Mitra</span>
                    </div>
                    <p class="font-body text-xs text-neutral-500">Tindak lanjuti calon mitra toko dan permohonan harga grosir.</p>
                </a>

                <a href="{{ route('admin.shipping.index') }}" class="p-4 rounded-lg bg-canvas-sunken border border-neutral-200 hover:border-primary-400 hover:bg-white transition block">
                    <div class="flex items-center gap-2 text-primary-700 font-semibold font-body text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <span>Zona Ongkir</span>
                    </div>
                    <p class="font-body text-xs text-neutral-500">Sesuaikan tarif dasar dan tarif per kg ekstra per wilayah pengiriman.</p>
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
