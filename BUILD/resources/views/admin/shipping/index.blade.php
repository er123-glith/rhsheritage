<x-layouts.admin>
    <x-slot:title>Zona & Tarif Ongkos Kirim</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Zona Ongkir</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Zona & Tarif Ongkir</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Atur pembagian zona wilayah pengiriman, tarif 1 kg pertama, dan tarif per kilogram tambahan untuk kalkulasi ongkos kirim pelanggan.
                </p>
            </div>
            <a href="{{ route('admin.shipping.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold transition shadow-sm self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Zona Baru</span>
            </a>
        </div>

        <!-- Info Card: Rumus Perhitungan Ongkir -->
        <div class="p-4 sm:p-5 rounded-xl bg-canvas-sunken border border-neutral-200/80 shadow-xs flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="font-body text-xs text-neutral-600 leading-relaxed">
                <p class="font-bold text-primary-900 text-sm">Rumus Kalkulasi Ongkir Otomatis (Sistem RHS HERITAGE):</p>
                <div class="mt-1 p-2 bg-white rounded-md border border-neutral-200 font-mono text-xs text-neutral-800 inline-block font-semibold">
                    Total Ongkir = Tarif Dasar + [Tarif Ekstra × (Jumlah Kg Dibulatkan - 1)]
                </div>
                <p class="text-neutral-500 mt-1.5">Berat pesanan selalu dibulatkan ke atas dengan batas minimal 1 kg (contoh: 250 gr dihitung 1 kg; 1.200 gr dihitung 2 kg). Tarif yang diperbarui langsung berlaku secara instan saat checkout.</p>
            </div>
        </div>

        <!-- Shipping Zones Table Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-canvas-sunken border-b border-neutral-200">
                        <tr>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center w-16">Urutan</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Nama Zona & Cakupan Wilayah</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Tarif 1 Kg Pertama</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Tarif / Kg Ekstra</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center">Status</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-center">Pesanan</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($zones as $zone)
                            <tr class="hover:bg-canvas-sunken/40 transition">
                                <!-- Sort Order -->
                                <td class="px-5 py-4 text-center font-mono text-xs font-bold text-neutral-500">
                                    {{ $zone->sort_order }}
                                </td>

                                <!-- Zone Name & Description -->
                                <td class="px-5 py-4">
                                    <div class="font-body text-sm font-bold text-neutral-900">
                                        {{ $zone->name }}
                                    </div>
                                    @if($zone->description)
                                        <div class="text-xs text-neutral-500 mt-0.5 max-w-md line-clamp-2">
                                            {{ $zone->description }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Base Rate -->
                                <td class="px-5 py-4 font-body text-sm font-bold text-primary-700">
                                    Rp{{ number_format($zone->base_rate, 0, ',', '.') }}
                                </td>

                                <!-- Extra Kg Rate -->
                                <td class="px-5 py-4 font-body text-sm text-neutral-800 font-semibold">
                                    +Rp{{ number_format($zone->rate_per_extra_kg, 0, ',', '.') }} <span class="text-xs text-neutral-400 font-normal">/ kg</span>
                                </td>

                                <!-- Active Status & Quick Toggle -->
                                <td class="px-5 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.shipping.toggle-active', $zone) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            title="Klik untuk mengubah status aktif"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition border {{ $zone->is_active ? 'bg-primary-50 text-primary-800 border-primary-200 hover:bg-primary-100' : 'bg-neutral-100 text-neutral-500 border-neutral-200 hover:bg-neutral-200' }}"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full {{ $zone->is_active ? 'bg-success' : 'bg-neutral-400' }}"></span>
                                            {{ $zone->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Order Count -->
                                <td class="px-5 py-4 text-center font-mono text-xs font-semibold text-neutral-600">
                                    {{ $zone->orders_count }} pesanan
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.shipping.edit', $zone) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                                            <span>Edit</span>
                                        </a>

                                        @if($zone->orders_count == 0)
                                            <form method="POST" action="{{ route('admin.shipping.destroy', $zone) }}" data-confirm="Hapus zona {{ $zone->name }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-neutral-400 hover:text-danger hover:bg-danger-bg transition" title="Hapus zona">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mx-auto mb-4 text-neutral-400">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                        </div>
                                        <p class="font-display font-bold text-neutral-800 text-base">Belum Ada Zona Pengiriman</p>
                                        <p class="font-body text-xs text-neutral-500 mt-1">Tambahkan zona pengiriman agar pelanggan dapat memilih tarif ongkir saat checkout.</p>
                                        <a href="{{ route('admin.shipping.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 text-white font-body text-xs font-semibold hover:bg-primary-800 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah Zona Pengiriman</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
