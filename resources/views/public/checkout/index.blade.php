<x-layouts.public>
    <x-slot:title>Form Pemesanan & Pembayaran — RHS HERITAGE</x-slot:title>

    <div class="space-y-8" x-data="{
        subtotal: {{ $summary->subtotal }},
        shippingCost: 0,
        selectedZoneId: '{{ old('shipping_zone_id') }}',
        calculating: false,
        submitting: false,

        get total() {
            return this.subtotal + this.shippingCost;
        },

        formatRupiah(num) {
            return 'Rp' + new Intl.NumberFormat('id-ID').format(num);
        },

        async updateShipping(zoneId) {
            if (!zoneId) {
                this.shippingCost = 0;
                return;
            }
            this.calculating = true;
            try {
                const res = await fetch('{{ route('checkout.shipping') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ zone_id: zoneId })
                });
                const data = await res.json();
                if (data.shipping_cost !== undefined) {
                    this.shippingCost = data.shipping_cost;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.calculating = false;
            }
        },

        init() {
            if (this.selectedZoneId) {
                this.updateShipping(this.selectedZoneId);
            }
        }
    }">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-body text-neutral-500" data-aos="fade-down" data-aos-duration="600">
            <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
            <span>/</span>
            <a href="{{ route('cart.index') }}" class="hover:text-primary-700">Keranjang</a>
            <span>/</span>
            <span class="text-primary-700 font-semibold">Checkout Pesanan</span>
        </nav>

        <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800" data-aos="fade-down" data-aos-delay="50">
            Form Pemesanan & Pembayaran
        </h1>

        <form action="{{ route('checkout.store') }}" method="POST" @submit="submitting = true">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Customer Details & Shipping Address (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-2xl border border-neutral-200 p-6 sm:p-8 shadow-1 space-y-6" data-aos="fade-right" data-aos-duration="650">
                    <div class="border-b border-neutral-100 pb-4">
                        <h2 class="font-display text-lg font-bold text-neutral-800">
                            1. Data Diri Pemesan
                        </h2>
                        <p class="font-body text-xs text-neutral-500 mt-1">
                            Pesanan akan dikonfirmasi langsung oleh owner melalui WhatsApp.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                required
                                placeholder="Contoh: Ahmad Fadilah"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('customer_name')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor HP / WhatsApp -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Nomor WhatsApp <span class="text-danger">*</span>
                            </label>
                            <input
                                type="tel"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                required
                                placeholder="Contoh: 08123456789"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('customer_phone')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-b border-neutral-100 pt-4 pb-4">
                        <h2 class="font-display text-lg font-bold text-neutral-800">
                            2. Alamat Pengiriman
                        </h2>
                        <p class="font-body text-xs text-neutral-500 mt-1">
                            Pastikan alamat selengkap mungkin agar pengiriman tepat sasaran.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Alamat Lengkap -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Alamat Jalan, RT/RW, Dusun/Kelurahan, Kecamatan <span class="text-danger">*</span>
                            </label>
                            <textarea
                                name="address_line"
                                rows="3"
                                required
                                placeholder="Contoh: Jl. Pesantren No. 12, RT 02/RW 04, Kel. Sukamaju, Kec. Cisarua"
                                class="w-full p-3 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >{{ old('address_line') }}</textarea>
                            @error('address_line')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Kota / Kabupaten -->
                            <div>
                                <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                    Kota / Kabupaten <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="address_city"
                                    value="{{ old('address_city') }}"
                                    required
                                    placeholder="Contoh: Bandung"
                                    class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                                >
                                @error('address_city')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                    Provinsi <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="address_province"
                                    value="{{ old('address_province') }}"
                                    required
                                    placeholder="Contoh: Jawa Barat"
                                    class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                                >
                                @error('address_province')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kode Pos -->
                            <div>
                                <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                    Kode Pos (5 Digit) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="address_postal_code"
                                    value="{{ old('address_postal_code') }}"
                                    required
                                    maxlength="5"
                                    placeholder="40123"
                                    class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                                >
                                @error('address_postal_code')
                                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Zona Pengiriman -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Pilih Wilayah / Zona Pengiriman <span class="text-danger">*</span>
                            </label>
                            <select
                                name="shipping_zone_id"
                                required
                                x-model="selectedZoneId"
                                @change="updateShipping($event.target.value)"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                                <option value="">-- Pilih Wilayah Ekspedisi --</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('shipping_zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }} (Tarif dasar Rp{{ number_format($zone->base_rate, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('shipping_zone_id')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan Khusus -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Catatan untuk Pengirim / Kurir (Opsional)
                            </label>
                            <input
                                type="text"
                                name="address_note"
                                value="{{ old('address_note') }}"
                                placeholder="Contoh: Titipkan di pos satpam bila rumah kosong"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('address_note')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Summary (4 cols) -->
                <div class="lg:col-span-4 bg-white rounded-2xl border border-neutral-200 p-6 shadow-1 space-y-6 sticky top-28" data-aos="fade-left" data-aos-delay="150" data-aos-duration="650">
                    <h2 class="font-display text-lg font-bold text-neutral-800 border-b border-neutral-100 pb-3">
                        Rincian Tagihan
                    </h2>

                    <!-- Items Preview -->
                    <div class="space-y-3 max-h-52 overflow-y-auto pr-1">
                        @foreach($summary->items as $item)
                            <div class="flex items-center justify-between text-xs font-body">
                                <div class="truncate mr-2">
                                    <p class="font-semibold text-neutral-800 truncate">{{ $item->variant->product->name }}</p>
                                    <p class="text-neutral-500 text-[11px]">{{ $item->variant->variant_label ?? $item->variant->sku }} &times; {{ $item->quantity }}</p>
                                </div>
                                <span class="font-semibold text-neutral-700 shrink-0">
                                    Rp{{ number_format($item->subtotal(), 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2.5 pt-3 border-t border-neutral-100 font-body text-xs text-neutral-600">
                        <div class="flex items-center justify-between">
                            <span>Subtotal Produk:</span>
                            <span class="font-semibold text-neutral-800">Rp{{ number_format($summary->subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span>Ongkos Kirim (Estimasi):</span>
                            <span class="font-semibold text-neutral-800" x-text="calculating ? 'Menghitung...' : formatRupiah(shippingCost)">
                                Rp0
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-neutral-200 text-sm">
                            <span class="font-bold text-neutral-800">Total Pembayaran:</span>
                            <span class="font-display font-bold text-xl text-primary-700" x-text="formatRupiah(total)">
                                Rp{{ number_format($summary->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-lg bg-primary-50 border border-primary-100 text-xs font-body text-primary-800 leading-relaxed">
                        <p class="font-semibold flex items-center gap-1 mb-1">
                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Alur Setelah Pemesanan
                        </p>
                        Setelah menekan tombol di bawah, Anda akan diarahkan ke konfirmasi pesanan dengan nomor order resmi serta tombol kirim pesan otomatis ke WhatsApp owner.
                    </div>

                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full shadow-md"
                        x-bind:disabled="submitting"
                    >
                        <span x-show="!submitting">Konfirmasi & Simpan Pesanan &rarr;</span>
                        <span x-show="submitting" x-cloak>Memproses Pesanan...</span>
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.public>
