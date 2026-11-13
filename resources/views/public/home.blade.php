<x-layouts.public>
    <x-slot:title>RHS HERITAGE — Kriya Songkok, Sarung ATBM & Jilbab Sekolah Sejak 2004</x-slot:title>

    <div class="space-y-8 sm:space-y-12">
        <!-- 1. Hero Promo Carousel & Quick Action Cards (Tokopedia Marketplace Style) -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch" data-aos="fade-up" data-aos-duration="700" x-data="{
            activeSlide: 0,
            slidesCount: 4,
            timer: null,
            init() {
                this.timer = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
                }, 5000);
            },
            goTo(index) {
                this.activeSlide = index;
                clearInterval(this.timer);
                this.timer = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
                }, 5000);
            }
        }">
            <!-- Main Promo Slider (8 cols) -->
            <div class="lg:col-span-8 relative rounded-2xl overflow-hidden shadow-2 bg-gradient-to-r from-primary-900 via-primary-800 to-primary-700 min-h-[300px] sm:min-h-[360px] flex items-center" data-aos="fade-right" data-aos-delay="50">
                <!-- Slides Container -->
                <div class="w-full h-full relative">
                    <!-- Slide 1: Kriya Utama -->
                    <div x-show="activeSlide === 0" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500 text-neutral-900 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-crown text-xs text-neutral-900"></i>
                                <span>Kriya Tradisi Pilihan Sejak 2004</span>
                            </span>
                            <h1 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Keanggunan Songkok & Sarung ATBM Tradisi
                            </h1>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Beludru halus tahan lama, tenun manual bernilai seni tinggi, langsung dari pengrajin keluarga tanpa perantara.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.index') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition transform active:scale-95 flex items-center gap-1.5">
                                    <span>Belanja Sekarang</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                                <a href="{{ route('catalog.category', 'songkok') }}" class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-body text-xs sm:text-sm font-semibold backdrop-blur-sm border border-white/20 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-crown text-xs"></i>
                                    <span>Lihat Songkok</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Grosir & Pesantren -->
                    <div x-show="activeSlide === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10" style="display: none;">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-700 border border-gold-400 text-gold-300 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-boxes-stacked text-xs text-gold-300"></i>
                                <span>Program Pasokan Grosir & Pesantren</span>
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Harga Grosir Bertingkat untuk Mitra & Sekolah
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Dapatkan penawaran harga pabrik untuk pengadaan seragam santri, toko busana muslim, dan yayasan pendidikan.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('partnership.index') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition transform active:scale-95 flex items-center gap-1.5">
                                    <i class="fa-solid fa-handshake text-xs"></i>
                                    <span>Ajukan Harga Grosir</span>
                                </a>
                                @php
                                    $waNumber = config('whatsapp.destination_number', '6285728979999');
                                    $waGrosirMsg = rawurlencode('Halo RHS HERITAGE, saya tertarik dengan kemitraan grosir dan pengadaan kriya.');
                                @endphp
                                <a href="https://wa.me/{{ $waNumber }}?text={{ $waGrosirMsg }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-body text-xs sm:text-sm font-semibold transition flex items-center gap-1.5">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>Konsultasi WA</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: Sarung ATBM -->
                    <div x-show="activeSlide === 2" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10" style="display: none;">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500 text-neutral-900 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-scroll text-xs text-neutral-900"></i>
                                <span>Kriya Tenun Nusantara</span>
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Sarung ATBM Sutra & Goyor Tradisi
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Tenun manual helai demi helai menggunakan Alat Tenun Bukan Mesin. Serat lembut, motif klasik, dan adem dipakai.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.category', 'sarung') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition flex items-center gap-1.5">
                                    <span>Koleksi Sarung ATBM</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4: Jilbab Seragam Sekolah -->
                    <div x-show="activeSlide === 3" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10" style="display: none;">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-700 border border-gold-400 text-gold-300 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                <i class="fa-solid fa-award text-xs text-gold-300"></i>
                                <span>Seragam Sekolah Berkualitas</span>
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Jilbab & Kerudung Seragam Sekolah
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Jahitan rapi, pet tidak mudah patah, bahan jatuh dan tidak panas untuk aktivitas belajar sehari-hari.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.category', 'jilbab') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition flex items-center gap-1.5">
                                    <span>Lihat Jilbab Sekolah</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Decorative Elements -->
                <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full border-8 border-gold-500/10 pointer-events-none"></div>

                <!-- Navigation Dots (Tokopedia Style) -->
                <div class="absolute bottom-4 left-6 sm:left-10 z-20 flex items-center gap-1.5">
                    <template x-for="i in slidesCount" :key="i">
                        <button @click="goTo(i - 1)" 
                                :class="activeSlide === (i - 1) ? 'w-6 bg-gold-400' : 'w-2 bg-white/50 hover:bg-white/80'" 
                                class="h-2 rounded-full transition-all duration-300 focus:outline-none" 
                                :aria-label="'Slide ' + i">
                        </button>
                    </template>
                </div>

                <!-- Prev/Next Controls -->
                <div class="absolute bottom-4 right-4 z-20 flex items-center gap-1">
                    <button @click="activeSlide = (activeSlide - 1 + slidesCount) % slidesCount" 
                            class="w-7 h-7 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur-xs transition flex items-center justify-center"
                            aria-label="Slide Sebelumnya">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <button @click="activeSlide = (activeSlide + 1) % slidesCount" 
                            class="w-7 h-7 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur-xs transition flex items-center justify-center"
                            aria-label="Slide Selanjutnya">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Side Cards (Tokopedia Mini Banners / Highlights - 4 cols) -->
            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4">
                <!-- Side Card 1: Kemitraan Grosir -->
                <a href="{{ route('partnership.index') }}" data-aos="fade-left" data-aos-delay="100" class="flex-1 rounded-2xl bg-gradient-to-br from-gold-500 to-amber-600 text-neutral-900 p-5 shadow-1 hover:shadow-2 hover:-translate-y-0.5 transition flex flex-col justify-between group overflow-hidden relative">
                    <div class="relative z-10">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-neutral-900 text-gold-400 font-body text-[10px] font-bold uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-briefcase text-[9px]"></i>
                            <span>B2B & Sekolah</span>
                        </span>
                        <h3 class="font-display text-lg font-bold text-neutral-900 leading-snug group-hover:underline">
                            Pesan Grosir & Kustom Seragam
                        </h3>
                        <p class="font-body text-xs text-neutral-800 mt-1 leading-relaxed">
                            Potongan harga khusus untuk pesanan bertahap & kuantitas kodi.
                        </p>
                    </div>
                    <div class="relative z-10 mt-4 flex items-center justify-between text-xs font-bold text-neutral-900">
                        <span>Cek Formulir Grosir</span>
                        <span class="w-6 h-6 rounded-full bg-neutral-900 text-white flex items-center justify-center group-hover:translate-x-1 transition-transform">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/20 pointer-events-none"></div>
                </a>

                <!-- Side Card 2: Lacak Pesanan & Cek Resi -->
                <a href="{{ route('order.track') }}" data-aos="fade-left" data-aos-delay="200" class="flex-1 rounded-2xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 p-5 shadow-1 hover:border-primary-400 dark:hover:border-gold-500 hover:shadow-2 hover:-translate-y-0.5 transition flex flex-col justify-between group overflow-hidden relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-gold-400 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-truck-fast text-[11px]"></i>
                            </span>
                            <span class="font-body text-[11px] font-bold uppercase tracking-wider text-primary-700 dark:text-gold-400">Lacak Pengiriman</span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-neutral-900 dark:text-white leading-snug group-hover:text-primary-700 dark:group-hover:text-gold-400 transition-colors">
                            Cek Status & Resi Pesanan
                        </h3>
                        <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                            Masukkan nomor pesanan untuk memantau proses pengiriman paket Anda.
                        </p>
                    </div>
                    <div class="relative z-10 mt-4 flex items-center justify-between text-xs font-bold text-primary-700 dark:text-gold-400">
                        <span>Lacak Sekarang</span>
                        <span class="w-6 h-6 rounded-full bg-primary-50 dark:bg-primary-900/60 text-primary-700 dark:text-gold-400 flex items-center justify-center group-hover:translate-x-1 transition-transform">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>
            </div>
        </section>

        <!-- 2. Kategori Pilihan & Layanan (Tokopedia Icon Grid Style) -->
        <section class="bg-white dark:bg-[#15221d] rounded-2xl p-4 sm:p-6 border border-neutral-200 dark:border-neutral-700 shadow-1" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-5 bg-primary-700 dark:bg-gold-500 rounded-full"></span>
                    <h2 class="font-display text-lg sm:text-xl font-bold text-neutral-900 dark:text-white">Kategori Pilihan & Layanan</h2>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-primary-700 dark:text-gold-400 hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4">
                <!-- Songkok Nasional -->
                <a href="{{ route('catalog.category', 'songkok') }}" data-aos="zoom-in" data-aos-delay="50" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-900/40 text-primary-700 dark:text-gold-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-primary-100 transition shadow-xs">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Songkok</span>
                    <span class="text-[10px] text-neutral-400">Nasional & AC</span>
                </a>

                <!-- Sarung ATBM -->
                <a href="{{ route('catalog.category', 'sarung') }}" data-aos="zoom-in" data-aos-delay="100" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gold-50 dark:bg-gold-950/40 text-gold-700 dark:text-gold-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-gold-100 transition shadow-xs">
                        <i class="fa-solid fa-scroll"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Sarung ATBM</span>
                    <span class="text-[10px] text-neutral-400">Tenun Manual</span>
                </a>

                <!-- Jilbab Sekolah -->
                <a href="{{ route('catalog.category', 'jilbab') }}" data-aos="zoom-in" data-aos-delay="150" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-rose-100 transition shadow-xs">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Jilbab</span>
                    <span class="text-[10px] text-neutral-400">Seragam Sekolah</span>
                </a>

                <!-- Paket Grosir -->
                <a href="{{ route('partnership.index') }}" data-aos="zoom-in" data-aos-delay="200" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-amber-100 transition shadow-xs">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Paket Grosir</span>
                    <span class="text-[10px] text-neutral-400">Harga Pabrik</span>
                </a>

                <!-- Toko Mitra -->
                <a href="{{ route('partnership.index', ['tab' => 'mitra']) }}" data-aos="zoom-in" data-aos-delay="250" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-emerald-100 transition shadow-xs">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Toko Mitra</span>
                    <span class="text-[10px] text-neutral-400">Ritel & Reseller</span>
                </a>

                <!-- Lacak Pesanan -->
                <a href="{{ route('order.track') }}" data-aos="zoom-in" data-aos-delay="300" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-sky-100 transition shadow-xs">
                        <i class="fa-solid fa-magnifying-glass-location"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Lacak Resi</span>
                    <span class="text-[10px] text-neutral-400">Status Paket</span>
                </a>

                <!-- Tentang Kriya -->
                <a href="{{ route('about') }}" data-aos="zoom-in" data-aos-delay="350" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-purple-100 transition shadow-xs">
                        <i class="fa-solid fa-landmark"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-primary-700 dark:group-hover:text-gold-400">Profil RHS</span>
                    <span class="text-[10px] text-neutral-400">Sejak 2004</span>
                </a>

                <!-- Konsultasi WA -->
                @php
                    $waHelpMsg = rawurlencode('Halo RHS HERITAGE, saya ingin bertanya mengenai ketersediaan produk dan konsultasi ukuran.');
                @endphp
                <a href="https://wa.me/{{ $waNumber }}?text={{ $waHelpMsg }}" target="_blank" rel="noopener noreferrer" data-aos="zoom-in" data-aos-delay="400" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 dark:hover:bg-neutral-800/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 group-hover:bg-emerald-100 transition shadow-xs">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 dark:text-neutral-200 mt-2 line-clamp-1 group-hover:text-emerald-700 dark:group-hover:text-emerald-400">Chat CS</span>
                    <span class="text-[10px] text-neutral-400">WhatsApp Fast</span>
                </a>
            </div>
        </section>

        <!-- 3. PRODUK TERLARIS (BEST SELLER) — Dihitung Berdasarkan Transaksi Aktual Database (Section H) -->
        <section class="rounded-2xl bg-neutral-900 dark:bg-[#111915] p-5 sm:p-7 text-white border border-neutral-800 dark:border-neutral-800 shadow-3" data-aos="fade-up" data-aos-duration="700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-base">
                            <i class="fa-solid fa-fire text-amber-500"></i>
                        </span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">Produk Terlaris</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-gold-500 text-neutral-950 font-body text-[11px] font-bold uppercase tracking-wider">
                            Best Seller
                        </span>
                    </div>
                    <p class="font-body text-xs text-neutral-400 mt-1">
                        Peringkat kriya paling diminati berdasarkan akumulasi kuantitas penjualan bersih yang sah dalam database transaksi RHS Heritage.
                    </p>
                </div>
                <a href="{{ route('catalog.index') }}" class="font-body text-xs font-bold text-gold-400 hover:text-gold-300 hover:underline self-start sm:self-auto flex items-center gap-1.5 shrink-0">
                    <span>Lihat Semua Katalog</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            @if($bestSellerProducts->isNotEmpty())
                <!-- Grid Produk Terlaris Aktual -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                    @foreach($bestSellerProducts as $index => $product)
                        @php
                            $activeVariants = $product->variants->where('is_active', true);
                            $minPrice = $activeVariants->min('price') ?? $product->base_price;
                            $stockQty = $activeVariants->sum('stock_quantity');
                        @endphp
                        <a href="{{ route('product.show', $product->slug) }}" data-aos="zoom-in" data-aos-delay="{{ 50 * ($index + 1) }}" class="bg-white dark:bg-[#15221d] rounded-xl p-3 text-neutral-800 dark:text-neutral-100 shadow-1 hover:shadow-2 hover:-translate-y-1 transition duration-200 flex flex-col justify-between group relative border border-neutral-200/80 dark:border-neutral-700/80">
                            <div>
                                <!-- Thumbnail with Rank Badge -->
                                <div class="relative aspect-square rounded-lg overflow-hidden bg-canvas-sunken dark:bg-neutral-800 mb-2.5">
                                    <!-- Sales Rank Tag -->
                                    <span class="absolute top-2 left-2 z-10 inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-body text-[10px] font-extrabold shadow-sm {{ $index === 0 ? 'bg-gold-500 text-neutral-950' : ($index === 1 ? 'bg-neutral-200 text-neutral-900' : 'bg-amber-700 text-white') }}">
                                        <i class="fa-solid fa-trophy text-[9px]"></i>
                                        <span>#{{ $product->sales_rank }} Best Seller</span>
                                    </span>

                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-neutral-400 text-xs">
                                            RHS HERITAGE
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Title -->
                                <h3 class="font-body font-semibold text-xs text-neutral-800 dark:text-neutral-100 line-clamp-2 leading-snug group-hover:text-primary-700 dark:group-hover:text-gold-400 transition">
                                    {{ $product->name }}
                                </h3>
                            </div>

                            <div class="mt-2.5 pt-2 border-t border-neutral-100 dark:border-neutral-800">
                                <!-- Actual Price from DB -->
                                <p class="font-body text-sm font-bold text-primary-700 dark:text-gold-400">
                                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                                </p>

                                <!-- Actual Units Sold Metric -->
                                <div class="mt-1.5 flex items-center justify-between text-[11px]">
                                    <span class="text-neutral-500 dark:text-neutral-400 flex items-center gap-1">
                                        <i class="fa-solid fa-bag-shopping text-[10px] text-amber-500"></i>
                                        <span>Terjual {{ $product->total_sold }} unit</span>
                                    </span>
                                </div>

                                <!-- Actual Stock Availability -->
                                <div class="mt-1.5">
                                    @if($stockQty > 0)
                                        <div class="flex items-center gap-1 text-[11px] text-emerald-700 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span>Stok: {{ $stockQty }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-[11px] text-red-600 dark:text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                                            <span>Habis</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Honest Alternative Section (Jika belum ada data transaksi yang memenuhi syarat) -->
                <div class="space-y-4">
                    <div class="p-6 rounded-xl bg-neutral-800/70 border border-neutral-700 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-neutral-700 text-gold-400 mx-auto flex items-center justify-center text-xl">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h3 class="font-display text-base font-bold text-white">Jelajahi Produk Pilihan Pengrajin</h3>
                        <p class="font-body text-xs text-neutral-400 max-w-md mx-auto">
                            Belum ada riwayat transaksi penjualan yang cukup untuk merangking produk terlaris. Berikut adalah koleksi katalog kriya yang dapat Anda pesan langsung.
                        </p>
                    </div>

                    @if($fallbackCatalogProducts->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                            @foreach($fallbackCatalogProducts as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </section>

        <!-- 4. Official Store Value Pillars (Tokopedia Keunggulan Belanja) -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" data-aos="fade-up" data-aos-duration="600">
            <div class="p-4 rounded-xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 shadow-1 flex items-center gap-3.5" data-aos="fade-up" data-aos-delay="50">
                <div class="w-11 h-11 rounded-xl bg-gold-50 dark:bg-gold-950/60 text-gold-700 dark:text-gold-400 flex items-center justify-center text-lg shrink-0 border border-gold-200 dark:border-gold-800">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900 dark:text-white">100% Kriya Orisinal</h4>
                    <p class="font-body text-[11px] text-neutral-500 dark:text-neutral-400">Karya pengrajin sejak 2004</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 shadow-1 flex items-center gap-3.5" data-aos="fade-up" data-aos-delay="100">
                <div class="w-11 h-11 rounded-xl bg-primary-50 dark:bg-primary-950/60 text-primary-700 dark:text-gold-400 flex items-center justify-center text-lg shrink-0 border border-primary-200 dark:border-primary-800">
                    <i class="fa-solid fa-bolt-lightning"></i>
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900 dark:text-white">Langsung Pengrajin</h4>
                    <p class="font-body text-[11px] text-neutral-500 dark:text-neutral-400">Harga tangan pertama terbaik</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 shadow-1 flex items-center gap-3.5" data-aos="fade-up" data-aos-delay="150">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0 border border-emerald-200 dark:border-emerald-800">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900 dark:text-white">Ongkir Transparan</h4>
                    <p class="font-body text-[11px] text-neutral-500 dark:text-neutral-400">Kalkulasi otomatis per zona</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-[#15221d] border border-neutral-200 dark:border-neutral-700 shadow-1 flex items-center gap-3.5" data-aos="fade-up" data-aos-delay="200">
                <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 flex items-center justify-center text-lg shrink-0 border border-sky-200 dark:border-sky-800">
                    <i class="fa-brands fa-whatsapp text-xl text-[#25D366]"></i>
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900 dark:text-white">Respon WA Cepat</h4>
                    <p class="font-body text-[11px] text-neutral-500 dark:text-neutral-400">Konsultasi ukuran & partai</p>
                </div>
            </div>
        </section>

        <!-- 5. Rekomendasi Untuk Anda (Tokopedia Tabbed Product Feed) -->
        <section x-data="{ currentTab: 'all' }" class="space-y-6" data-aos="fade-up" data-aos-duration="600">
            <!-- Header & Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200 dark:border-neutral-700 pb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-5 bg-gold-500 rounded-full"></span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-900 dark:text-white">Rekomendasi Untuk Anda</h2>
                    </div>
                    <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">Koleksi kriya pilihan pengrajin dengan kualitas bahan dan jahitan terbaik</p>
                </div>

                <!-- Tokopedia Style Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button @click="currentTab = 'all'" 
                            :class="currentTab === 'all' ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-sm' : 'bg-canvas-sunken dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition">
                        Semua Kategori
                    </button>
                    <button @click="currentTab = 'songkok'" 
                            :class="currentTab === 'songkok' ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-sm' : 'bg-canvas-sunken dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                        <i class="fa-solid fa-crown text-[10px]"></i>
                        <span>Songkok Nusantara</span>
                    </button>
                    <button @click="currentTab = 'sarung'" 
                            :class="currentTab === 'sarung' ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-sm' : 'bg-canvas-sunken dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                        <i class="fa-solid fa-scroll text-[10px]"></i>
                        <span>Sarung ATBM</span>
                    </button>
                    <button @click="currentTab = 'jilbab'" 
                            :class="currentTab === 'jilbab' ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-sm' : 'bg-canvas-sunken dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300 hover:bg-neutral-200 dark:hover:bg-neutral-700'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                        <i class="fa-solid fa-award text-[10px]"></i>
                        <span>Jilbab Sekolah</span>
                    </button>
                </div>
            </div>

            <!-- Tab 1: All Featured Products Grid -->
            <div x-show="currentTab === 'all'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-12 text-center text-neutral-400">
                        Belum ada produk kriya yang tersedia.
                    </div>
                @endforelse
            </div>

            <!-- Tab 2: Songkok Products Grid -->
            <div x-show="currentTab === 'songkok'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4" style="display: none;">
                @forelse($songkokProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-12 text-center text-neutral-400">
                        Koleksi Songkok sedang disiapkan oleh pengrajin.
                    </div>
                @endforelse
            </div>

            <!-- Tab 3: Sarung Products Grid -->
            <div x-show="currentTab === 'sarung'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4" style="display: none;">
                @forelse($sarungProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-12 text-center text-neutral-400">
                        Koleksi Sarung ATBM sedang disiapkan oleh pengrajin.
                    </div>
                @endforelse
            </div>

            <!-- Tab 4: Jilbab Products Grid -->
            <div x-show="currentTab === 'jilbab'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4" style="display: none;">
                @forelse($jilbabProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-12 text-center text-neutral-400">
                        Koleksi Jilbab Sekolah sedang disiapkan oleh pengrajin.
                    </div>
                @endforelse
            </div>

            <div class="text-center pt-4" data-aos="fade-up">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-primary-700 dark:border-gold-500 text-primary-700 dark:text-gold-400 hover:bg-primary-700 hover:text-white dark:hover:bg-gold-500 dark:hover:text-neutral-900 font-body text-xs sm:text-sm font-bold transition shadow-sm">
                    <span>Lihat Seluruh Koleksi Katalog ({{ $featuredProducts->count() }}+ Produk)</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>

        <!-- 6. Tokopedia B2B Mitra Banner Callout -->
        <section class="rounded-2xl bg-gradient-to-r from-primary-900 via-primary-800 to-primary-900 text-white p-6 sm:p-10 border border-gold-500/30 shadow-2 relative overflow-hidden" data-aos="zoom-in" data-aos-duration="700">
            <div class="relative z-10 max-w-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 rounded bg-gold-500 text-neutral-900 font-body text-xs font-bold uppercase tracking-wider">
                        Program Mitra & Grosir
                    </span>
                    <span class="text-xs text-gold-300 font-medium">Pengadaan Pesantren & Toko</span>
                </div>

                <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mb-3">
                    Solusi Pasokan Kriya untuk Instansi & Toko Ritel
                </h2>

                <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                    Menerima pesanan dalam jumlah besar (kodi/lusin) dengan harga bertingkat, custom bordir nama yayasan pada songkok & jilbab, serta jaminan kualitas jahitan rapi berstandar nasional.
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="{{ route('partnership.index', ['tab' => 'grosir']) }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        <span>Ajukan Permohonan Grosir</span>
                    </a>
                    <a href="{{ route('partnership.index', ['tab' => 'mitra']) }}" class="px-5 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white border border-white/30 font-body text-xs sm:text-sm font-semibold transition text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-handshake text-xs"></i>
                        <span>Daftar Toko Mitra</span>
                    </a>
                </div>
            </div>

            <!-- Background Decorative Accent -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full border-8 border-gold-500/15 pointer-events-none"></div>
        </section>

        <!-- 7. ULASAN & KEPERCAYAAN PELANGGAN — Dihitung Berdasarkan Data Aktual Database (Section F) -->
        <section class="bg-white dark:bg-[#15221d] rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700 shadow-1 space-y-6" data-aos="fade-up" data-aos-duration="600">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-5 bg-gold-500 rounded-full"></span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-900 dark:text-white">Ulasan & Kepercayaan Pelanggan</h2>
                    </div>
                    <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        Pengalaman langsung dari pembeli kriya berstatus pesanan terverifikasi tanpa manipulasi data.
                    </p>
                </div>
                @if($reviewCount > 0)
                    <div class="flex items-center gap-1.5 text-gold-500 font-bold text-sm bg-gold-50 dark:bg-gold-950/60 px-3 py-1.5 rounded-xl border border-gold-300/60">
                        <i class="fa-solid fa-star text-gold-500"></i>
                        <span class="text-neutral-900 dark:text-white">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400 font-normal">({{ $reviewCount }} Ulasan)</span>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($publishedReviews as $index => $rev)
                    <div class="p-5 rounded-xl bg-canvas-sunken/60 dark:bg-[#111915] border border-neutral-200/80 dark:border-neutral-700/80 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="{{ 100 * ($index + 1) }}">
                        <div>
                            <!-- Star Rating -->
                            <div class="flex items-center gap-1 text-gold-500 text-xs mb-2.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $rev->rating ? 'fa-solid fa-star' : 'fa-regular fa-star text-neutral-300 dark:text-neutral-600' }}"></i>
                                @endfor
                                <span class="ml-1 text-[11px] font-bold text-neutral-600 dark:text-neutral-300">{{ $rev->rating }}.0</span>
                            </div>

                            <!-- Escaped Blade Text Output -->
                            <p class="font-body text-xs text-neutral-700 dark:text-neutral-300 italic leading-relaxed">
                                "{{ $rev->review }}"
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <!-- Masked Safe Customer Name -->
                                <p class="font-body text-xs font-bold text-neutral-800 dark:text-neutral-200">
                                    {{ $rev->masked_customer_name }}
                                </p>
                                <p class="text-[10px] text-neutral-400 mt-0.5">
                                    {{ $rev->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            @if($rev->isVerifiedPurchase())
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    <span>Pembelian Terverifikasi</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Empty State Bersih Sesuai Ketentuan Section F.6 -->
                    <div class="col-span-full py-12 px-6 rounded-xl bg-canvas-sunken/40 dark:bg-[#111915] border border-neutral-200 dark:border-neutral-800 text-center space-y-3">
                        <div class="w-14 h-14 rounded-full bg-gold-100 dark:bg-gold-950 text-gold-600 dark:text-gold-400 mx-auto flex items-center justify-center text-2xl">
                            <i class="fa-regular fa-comment-dots"></i>
                        </div>
                        <div>
                            <h4 class="font-display text-base font-bold text-neutral-800 dark:text-neutral-200">Belum Ada Ulasan Pelanggan</h4>
                            <p class="font-body text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 max-w-md mx-auto mt-1">
                                Belum ada ulasan pelanggan. Jadilah pelanggan pertama yang membagikan pengalaman berbelanja di RHS Heritage.
                            </p>
                        </div>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <div class="pt-2">
                                    <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold transition shadow-xs">
                                        <i class="fa-solid fa-box text-xs"></i>
                                        <span>Beri Ulasan dari Pesanan Selesai</span>
                                    </a>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 8. Tokopedia Style SEO & Heritage Story Box -->
        <section class="bg-white dark:bg-[#15221d] rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700 shadow-1 space-y-4" data-aos="fade-up" data-aos-duration="600">
            <h3 class="font-display text-lg font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-landmark text-primary-700 dark:text-gold-400 text-base"></i>
                <span>Pusat Kriya Songkok Nasional, Sarung ATBM & Seragam Jilbab Berkualitas — RHS HERITAGE</span>
            </h3>
            <div class="font-body text-xs text-neutral-600 dark:text-neutral-300 leading-relaxed space-y-3">
                <p>
                    Selamat datang di toko resmi <strong>RHS HERITAGE</strong>, spesialis kriya tradisi busana muslim keluarga yang telah dipercaya sejak tahun 2004. Kami memproduksi beragam produk unggulan meliputi <strong>Songkok Nasional</strong> berkualitas beludru halus dengan model AC ventilasi udara maupun standar, <strong>Sarung Alat Tenun Bukan Mesin (ATBM)</strong> yang dibuat dengan teknik tenun manual bernilai seni tinggi, serta <strong>Jilbab Seragam Sekolah</strong> yang dirancang dengan pet rapi, jahitan presisi, dan bahan adem untuk kenyamanan belajar.
                </p>
                <p>
                    Melalui website ini, Anda dapat memesan produk secara langsung ke pengrajin dengan tarif ongkir transparan, lacak resi otomatis, atau mengajukan pemesanan partai besar/grosir untuk madrasah, pesantren, dan toko mitra ritel busana muslim di seluruh Indonesia.
                </p>
            </div>
        </section>
    </div>
</x-layouts.public>
