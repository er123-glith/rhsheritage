<x-layouts.admin>
    <x-slot:title>Tambah Zona Pengiriman</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div>
            <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('admin.shipping.index') }}" class="hover:text-primary-700 transition">Zona Ongkir</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-700 font-medium">Tambah Zona Baru</span>
            </nav>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Tambah Zona Pengiriman</h1>
            <p class="font-body text-sm text-neutral-500 mt-1">Definisikan zona wilayah dan tarif ongkos kirim baru untuk proses checkout pembeli.</p>
        </div>

        <!-- Form Card -->
        <form method="POST" action="{{ route('admin.shipping.store') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1">
                    Nama Zona <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="Contoh: Zona 1 - Surabaya Raya / Jawa Timur"
                    class="w-full h-11 px-4 rounded-lg font-body text-sm border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 @error('name') border-danger @enderror"
                />
                @error('name')
                    <p class="mt-1 text-xs text-danger font-body">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description / Coverage Area -->
            <div>
                <label for="description" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1">
                    Cakupan Wilayah / Keterangan
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Contoh: Meliputi Kota Surabaya, Kabupaten Sidoarjo, dan Kabupaten Gresik"
                    class="w-full p-4 rounded-lg font-body text-sm border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 @error('description') border-danger @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-danger font-body">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rates Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Base Rate (1st kg) -->
                <div>
                    <label for="base_rate" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1">
                        Tarif Dasar (1 Kg Pertama) <span class="text-danger">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-sm text-neutral-500 font-semibold font-body">Rp</span>
                        <input
                            type="number"
                            id="base_rate"
                            name="base_rate"
                            value="{{ old('base_rate', 10000) }}"
                            step="500"
                            min="0"
                            required
                            class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm font-semibold border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 @error('base_rate') border-danger @enderror"
                        />
                    </div>
                    @error('base_rate')
                        <p class="mt-1 text-xs text-danger font-body">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rate per extra kg -->
                <div>
                    <label for="rate_per_extra_kg" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1">
                        Tarif / Kg Ekstra <span class="text-danger">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-sm text-neutral-500 font-semibold font-body">Rp</span>
                        <input
                            type="number"
                            id="rate_per_extra_kg"
                            name="rate_per_extra_kg"
                            value="{{ old('rate_per_extra_kg', 5000) }}"
                            step="500"
                            min="0"
                            required
                            class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm font-semibold border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 @error('rate_per_extra_kg') border-danger @enderror"
                        />
                    </div>
                    @error('rate_per_extra_kg')
                        <p class="mt-1 text-xs text-danger font-body">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sort Order & Active Checkbox -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="sort_order" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-1">
                        Nomor Urut Tampilan <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', $nextOrder) }}"
                        min="0"
                        required
                        class="w-full h-11 px-4 rounded-lg font-body text-sm border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 @error('sort_order') border-danger @enderror"
                    />
                    @error('sort_order')
                        <p class="mt-1 text-xs text-danger font-body">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center sm:pt-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', true))
                            class="w-4 h-4 text-primary-700 rounded border-neutral-300 focus:ring-primary-500"
                        />
                        <span class="font-body text-sm font-medium text-neutral-700">Aktifkan zona ini sekarang</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-100">
                <a href="{{ route('admin.shipping.index') }}" class="px-4 py-2.5 rounded-lg font-body text-sm font-semibold text-neutral-600 hover:bg-neutral-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-bold shadow-sm transition">
                    Simpan Zona
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
