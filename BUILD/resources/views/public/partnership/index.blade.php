<x-layouts.public>
    <x-slot:title>Harga Grosir & Program Toko Mitra — RHS HERITAGE</x-slot:title>

    <div class="space-y-12" x-data="{ tab: '{{ old('type', $activeTab) }}' }">
        <!-- Hero Header -->
        <div class="rounded-2xl bg-gradient-to-r from-primary-800 to-primary-900 text-white p-8 sm:p-12 border border-gold-500/30 shadow-2 text-center max-w-4xl mx-auto space-y-4" data-aos="zoom-in" data-aos-duration="650">
            <span class="inline-block px-3 py-1 rounded-full bg-gold-500 text-neutral-900 font-body text-xs font-bold uppercase tracking-wider">
                Kerjasama Resmi RHS HERITAGE
            </span>

            <h1 class="font-display text-2xl sm:text-4xl font-bold text-white leading-tight">
                Harga Grosir & Peluang Toko Mitra Resmi
            </h1>

            <p class="font-body text-sm text-primary-100 max-w-2xl mx-auto leading-relaxed">
                Kami membuka kemitraan untuk toko perlengkapan muslim, koperasi pesantren, yayasan pendidikan, serta madrasah/sekolah di seluruh Nusantara dengan harga langsung pengrajin.
            </p>

            <!-- Tab Switcher -->
            <div class="inline-flex p-1.5 rounded-full bg-primary-950/60 border border-gold-500/40 mt-4">
                <button
                    type="button"
                    @click="tab = 'grosir'"
                    class="px-6 py-2.5 rounded-full font-body text-xs sm:text-sm font-semibold transition-all duration-160"
                    :class="tab === 'grosir' ? 'bg-gold-500 text-neutral-900 shadow-sm font-bold' : 'text-primary-200 hover:text-white'"
                >
                    Pembelian Grosir
                </button>
                <button
                    type="button"
                    @click="tab = 'mitra'"
                    class="px-6 py-2.5 rounded-full font-body text-xs sm:text-sm font-semibold transition-all duration-160"
                    :class="tab === 'mitra' ? 'bg-gold-500 text-neutral-900 shadow-sm font-bold' : 'text-primary-200 hover:text-white'"
                >
                    Program Toko Mitra
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start max-w-5xl mx-auto">
            <!-- Left: Information Content (5 cols) -->
            <div class="lg:col-span-5 space-y-6" data-aos="fade-right" data-aos-delay="100" data-aos-duration="650">
                <!-- Tab: Grosir Info -->
                <div x-show="tab === 'grosir'" class="bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-4">
                    <h2 class="font-display text-xl font-bold text-neutral-800">
                        {{ $grosirContent->title ?? 'Ketentuan Pembelian Grosir' }}
                    </h2>
                    <div class="prose prose-sm text-neutral-600 font-body leading-relaxed">
                        {!! $grosirContent->body ?? '<p>Harga khusus bertingkat sesuai volume order songkok, sarung ATBM, maupun seragam jilbab madrasah.</p>' !!}
                    </div>

                    <div class="p-4 rounded-xl bg-gold-50 border border-gold-200 text-gold-900 text-xs font-body space-y-1.5">
                        <p class="font-bold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-600"></span>
                            Keuntungan Pembelian Grosir:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-gold-800">
                            <li>Harga bertingkat kompetitif langsung dari produsen.</li>
                            <li>Bisa pesan custom ukuran dan logo bordir sekolah/pesantren.</li>
                            <li>Ekspedisi kargo hemat ke seluruh Indonesia.</li>
                        </ul>
                    </div>
                </div>

                <!-- Tab: Mitra Info -->
                <div x-show="tab === 'mitra'" x-cloak class="bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-4">
                    <h2 class="font-display text-xl font-bold text-neutral-800">
                        {{ $mitraContent->title ?? 'Program Toko Mitra RHS HERITAGE' }}
                    </h2>
                    <div class="prose prose-sm text-neutral-600 font-body leading-relaxed">
                        {!! $mitraContent->body ?? '<p>Bermitra memasarkan produk songkok, sarung ATBM, dan jilbab sekolah di kota Anda.</p>' !!}
                    </div>

                    <div class="p-4 rounded-xl bg-primary-50 border border-primary-200 text-primary-900 text-xs font-body space-y-1.5">
                        <p class="font-bold flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-700"></span>
                            Syarat & Keuntungan Mitra:
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-primary-800">
                            <li>Memiliki toko fisik atau jaringan distribusi aktif di daerah.</li>
                            <li>Katalog fisik, banner promo, dan materi display resmi.</li>
                            <li>Prioritas alokasi produksi menjelang Ramadhan & Idul Fitri.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Application Form (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-6" data-aos="fade-left" data-aos-delay="150" data-aos-duration="650">
                <div class="border-b border-neutral-100 pb-3">
                    <h2 class="font-display text-xl font-bold text-neutral-800">
                        Formulir Pengajuan Kerjasama
                    </h2>
                    <p class="font-body text-xs text-neutral-500 mt-1">
                        Isi data berikut dengan lengkap. Tim owner RHS HERITAGE akan menghubungi Anda dalam 1-2 hari kerja.
                    </p>
                </div>

                <form action="{{ route('partnership.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Type (grosir / mitra) bound to tab -->
                    <input type="hidden" name="type" :value="tab">

                    <!-- Anti-spam Honeypot (SEC-13) -->
                    <div class="hidden" style="display: none;">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Pemohon -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Nama Lengkap Anda <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="applicant_name"
                                value="{{ old('applicant_name') }}"
                                required
                                placeholder="Contoh: H. Syamsudin"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('applicant_name')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama Toko / Institusi -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Nama Toko / Lembaga (Opsional)
                            </label>
                            <input
                                type="text"
                                name="institution_name"
                                value="{{ old('institution_name') }}"
                                placeholder="Contoh: Toko Berkah / Ponpes Al-Falah"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor WhatsApp -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Nomor WhatsApp Aktif <span class="text-danger">*</span>
                            </label>
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                                placeholder="Contoh: 08123456789"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('phone')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Email (Opsional)
                            </label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="alamat@email.com"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    <!-- Kota / Wilayah -->
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                            Kota / Kabupaten / Provinsi Domisili <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="region"
                            value="{{ old('region') }}"
                            required
                            placeholder="Contoh: Surabaya, Jawa Timur"
                            class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                        >
                        @error('region')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Produk yang Diminati -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Produk yang Diminati <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="product_interest"
                                value="{{ old('product_interest') }}"
                                required
                                placeholder="Contoh: Songkok AC & Sarung ATBM"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('product_interest')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimasi Kuantitas -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                                Perkiraan Kuantitas <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="estimated_quantity"
                                value="{{ old('estimated_quantity') }}"
                                required
                                placeholder="Contoh: 10 kodi / 200 pcs per bulan"
                                class="w-full h-11 px-3.5 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                            >
                            @error('estimated_quantity')
                                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pesan Tambahan -->
                    <div>
                        <label class="block font-body text-xs font-semibold text-neutral-700 mb-1.5">
                            Catatan atau Pertanyaan Tambahan
                        </label>
                        <textarea
                            name="message"
                            rows="3"
                            placeholder="Tuliskan spesifikasi khusus atau rencana pembukaan toko..."
                            class="w-full p-3 rounded-md bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-500 focus:outline-none"
                        >{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <x-button type="submit" variant="gold" size="lg" class="w-full shadow-md">
                            Kirim Pengajuan Kemitraan &rarr;
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.public>
