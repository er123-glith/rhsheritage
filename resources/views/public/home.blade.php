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
                                👑 Kriya Tradisi Pilihan Sejak 2004
                            </span>
                            <h1 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Keanggunan Songkok & Sarung ATBM Tradisi
                            </h1>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Beludru halus tahan lama, tenun manual bernilai seni tinggi, langsung dari pengrajin keluarga tanpa perantara.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.index') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition transform active:scale-95">
                                    Belanja Sekarang &rarr;
                                </a>
                                <a href="{{ route('catalog.category', 'songkok') }}" class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-body text-xs sm:text-sm font-semibold backdrop-blur-sm border border-white/20 transition">
                                    Lihat Songkok
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2: Grosir & Pesantren -->
                    <div x-show="activeSlide === 1" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-700 border border-gold-400 text-gold-300 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                📦 Program Pasokan Grosir & Pesantren
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Harga Grosir Bertingkat untuk Mitra & Sekolah
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Dapatkan penawaran harga pabrik untuk pengadaan seragam santri, toko busana muslim, dan yayasan pendidikan.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('partnership.index') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition transform active:scale-95">
                                    Ajukan Harga Grosir
                                </a>
                                <a href="https://wa.me/6285728979999?text=Halo%20RHS%20HERITAGE%2C%20saya%20tertarik%20dengan%20kemitraan%20grosir" target="_blank" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-body text-xs sm:text-sm font-semibold transition flex items-center gap-1.5">
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
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-500 text-neutral-900 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                🧵 Kriya Tenun Nusantara
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Sarung ATBM Sutra & Goyor Tradisi
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Tenun manual helai demi helai menggunakan Alat Tenun Bukan Mesin. Serat lembut, motif klasik, dan adem dipakai.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.category', 'sarung') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition">
                                    Koleksi Sarung ATBM &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4: Jilbab Seragam Sekolah -->
                    <div x-show="activeSlide === 3" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="p-6 sm:p-10 flex flex-col justify-center h-full text-white relative z-10">
                        <div class="max-w-xl">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-700 border border-gold-400 text-gold-300 font-body text-[11px] font-bold uppercase tracking-wider mb-3">
                                🧕 Seragam Sekolah Berkualitas
                            </span>
                            <h2 class="font-display text-2xl sm:text-4xl font-bold leading-tight mb-2 text-white">
                                Jilbab & Kerudung Seragam Sekolah
                            </h2>
                            <p class="font-body text-xs sm:text-sm text-primary-100 leading-relaxed mb-6">
                                Jahitan rapi, pet tidak mudah patah, bahan jatuh dan tidak panas untuk aktivitas belajar sehari-hari.
                            </p>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('catalog.category', 'jilbab') }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition">
                                    Lihat Jilbab Sekolah &rarr;
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
                            class="p-1.5 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur-xs transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="activeSlide = (activeSlide + 1) % slidesCount" 
                            class="p-1.5 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur-xs transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Side Cards (Tokopedia Mini Banners / Highlights - 4 cols) -->
            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4">
                <!-- Side Card 1: Kemitraan Grosir -->
                <a href="{{ route('partnership.index') }}" data-aos="fade-left" data-aos-delay="100" class="flex-1 rounded-2xl bg-gradient-to-br from-gold-500 to-amber-600 text-neutral-900 p-5 shadow-1 hover:shadow-2 hover:-translate-y-0.5 transition flex flex-col justify-between group overflow-hidden relative">
                    <div class="relative z-10">
                        <span class="inline-block px-2.5 py-0.5 rounded bg-neutral-900 text-gold-400 font-body text-[10px] font-bold uppercase tracking-wider mb-2">
                            B2B & Sekolah
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
                        <span class="w-6 h-6 rounded-full bg-neutral-900 text-white flex items-center justify-center group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-white/20 pointer-events-none"></div>
                </a>

                <!-- Side Card 2: Lacak Pesanan & Cek Resi -->
                <a href="{{ route('order.track') }}" data-aos="fade-left" data-aos-delay="200" class="flex-1 rounded-2xl bg-white border border-neutral-200 p-5 shadow-1 hover:border-primary-400 hover:shadow-2 hover:-translate-y-0.5 transition flex flex-col justify-between group overflow-hidden relative">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold">🚚</span>
                            <span class="font-body text-[11px] font-bold uppercase tracking-wider text-primary-700">Lacak Pengiriman</span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-neutral-900 leading-snug group-hover:text-primary-700 transition-colors">
                            Cek Status & Resi Pesanan
                        </h3>
                        <p class="font-body text-xs text-neutral-500 mt-1">
                            Masukkan nomor pesanan untuk memantau proses pengiriman paket Anda.
                        </p>
                    </div>
                    <div class="relative z-10 mt-4 flex items-center justify-between text-xs font-bold text-primary-700">
                        <span>Lacak Sekarang</span>
                        <span class="w-6 h-6 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center group-hover:translate-x-1 transition-transform">→</span>
                    </div>
                </a>
            </div>
        </section>

        <!-- 2. Kategori Pilihan / Quick Shortcuts (Tokopedia Icon Grid Style) -->
        <section class="bg-white rounded-2xl p-4 sm:p-6 border border-neutral-200 shadow-1" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-5 bg-primary-700 rounded-full"></span>
                    <h2 class="font-display text-lg sm:text-xl font-bold text-neutral-900">Kategori Pilihan & Layanan</h2>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-xs font-semibold text-primary-700 hover:text-primary-800 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-4 lg:grid-cols-8 gap-3 sm:gap-4">
                <!-- Songkok Nasional -->
                <a href="{{ route('catalog.category', 'songkok') }}" data-aos="zoom-in" data-aos-delay="50" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-primary-100 transition shadow-xs">
                        👑
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Songkok</span>
                    <span class="text-[10px] text-neutral-400">Nasional & AC</span>
                </a>

                <!-- Sarung ATBM -->
                <a href="{{ route('catalog.category', 'sarung') }}" data-aos="zoom-in" data-aos-delay="100" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gold-50 text-gold-800 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-gold-100 transition shadow-xs">
                        🧵
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Sarung ATBM</span>
                    <span class="text-[10px] text-neutral-400">Tenun Manual</span>
                </a>

                <!-- Jilbab Sekolah -->
                <a href="{{ route('catalog.category', 'jilbab') }}" data-aos="zoom-in" data-aos-delay="150" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-rose-100 transition shadow-xs">
                        🧕
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Jilbab</span>
                    <span class="text-[10px] text-neutral-400">Seragam Sekolah</span>
                </a>

                <!-- Paket Grosir -->
                <a href="{{ route('partnership.index') }}" data-aos="zoom-in" data-aos-delay="200" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-100 transition shadow-xs">
                        📦
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Paket Grosir</span>
                    <span class="text-[10px] text-neutral-400">Harga Pabrik</span>
                </a>

                <!-- Toko Mitra -->
                <a href="{{ route('partnership.index', ['tab' => 'mitra']) }}" data-aos="zoom-in" data-aos-delay="250" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-100 transition shadow-xs">
                        🤝
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Toko Mitra</span>
                    <span class="text-[10px] text-neutral-400">Ritel & Reseller</span>
                </a>

                <!-- Lacak Pesanan -->
                <a href="{{ route('order.track') }}" data-aos="zoom-in" data-aos-delay="300" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-sky-100 transition shadow-xs">
                        🔍
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Lacak Resi</span>
                    <span class="text-[10px] text-neutral-400">Status Paket</span>
                </a>

                <!-- Tentang Kriya -->
                <a href="{{ route('about') }}" data-aos="zoom-in" data-aos-delay="350" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-purple-100 transition shadow-xs">
                        🏛️
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-primary-700">Profil RHS</span>
                    <span class="text-[10px] text-neutral-400">Sejak 2004</span>
                </a>

                <!-- Konsultasi WA -->
                <a href="https://wa.me/6285728979999?text=Halo%20RHS%20HERITAGE%2C%20saya%20ingin%20bertanya%20mengenai%20produk" target="_blank" data-aos="zoom-in" data-aos-delay="400" class="flex flex-col items-center text-center p-3 rounded-xl hover:bg-canvas-sunken/60 hover:border-primary-300 border border-transparent transition group">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-100 transition shadow-xs">
                        💬
                    </div>
                    <span class="font-body text-xs font-semibold text-neutral-800 mt-2 line-clamp-1 group-hover:text-emerald-700">Chat CS</span>
                    <span class="text-[10px] text-neutral-400">WhatsApp Fast</span>
                </a>
            </div>
        </section>

        <!-- 3. Tokopedia-Style "Kejar Diskon / Penawaran Terbaik Hari Ini" -->
        @if($flashSaleProducts->isNotEmpty())
            <section class="rounded-2xl bg-neutral-900 dark:bg-black p-4 sm:p-6 text-white border border-neutral-800 dark:border-neutral-800 shadow-3" data-aos="fade-up" data-aos-duration="700" x-data="{
                hours: 8,
                minutes: 45,
                seconds: 30,
                init() {
                    setInterval(() => {
                        if (this.seconds > 0) {
                            this.seconds--;
                        } else {
                            this.seconds = 59;
                            if (this.minutes > 0) {
                                this.minutes--;
                            } else {
                                this.minutes = 59;
                                if (this.hours > 0) this.hours--;
                            }
                        }
                    }, 1000);
                }
            }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🔥</span>
                            <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">Penawaran Eksklusif Pengrajin</h2>
                        </div>
                        <!-- Live Countdown Timer -->
                        <div class="flex items-center gap-1.5 font-mono text-xs font-bold">
                            <span class="text-xs text-neutral-400 uppercase hidden md:inline">Berakhir dalam:</span>
                            <span class="px-2 py-1 rounded bg-neutral-800 text-gold-400 border border-neutral-700 shadow-xs" x-text="String(hours).padStart(2, '0')">08</span>
                            <span class="text-neutral-400">:</span>
                            <span class="px-2 py-1 rounded bg-neutral-800 text-gold-400 border border-neutral-700 shadow-xs" x-text="String(minutes).padStart(2, '0')">45</span>
                            <span class="text-neutral-400">:</span>
                            <span class="px-2 py-1 rounded bg-neutral-800 text-gold-400 border border-neutral-700 shadow-xs" x-text="String(seconds).padStart(2, '0')">30</span>
                        </div>
                    </div>
                    <a href="{{ route('catalog.index') }}" class="font-body text-xs font-bold text-gold-400 hover:text-gold-300 hover:underline self-start sm:self-auto">
                        Lihat Semua Promo &rarr;
                    </a>
                </div>

                <!-- Horizontal Product Slider / Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                    @foreach($flashSaleProducts as $index => $product)
                        @php
                            $activeVariants = $product->variants->where('is_active', true);
                            $minPrice = $activeVariants->min('price') ?? $product->base_price;
                            $fakeDiscount = 15;
                            $originalPrice = $minPrice * 1.15;
                            $stockQty = $activeVariants->sum('stock_quantity');
                        @endphp
                        <a href="{{ route('product.show', $product->slug) }}" data-aos="zoom-in" data-aos-delay="{{ 50 * ($index + 1) }}" class="bg-white rounded-xl p-3 text-neutral-800 shadow-1 hover:shadow-2 hover:-translate-y-1 transition duration-200 flex flex-col justify-between group">
                            <div>
                                <!-- Thumbnail -->
                                <div class="relative aspect-square rounded-lg overflow-hidden bg-canvas-sunken mb-2">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-neutral-400 text-xs">
                                            RHS HERITAGE
                                        </div>
                                    @endif
                                    <!-- Discount Badge -->
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-red-500 text-white font-mono text-[10px] font-bold">
                                        -{{ $fakeDiscount }}%
                                    </span>
                                </div>

                                <!-- Product Title -->
                                <h3 class="font-body font-semibold text-xs text-neutral-800 line-clamp-2 leading-snug group-hover:text-primary-700 transition">
                                    {{ $product->name }}
                                </h3>
                            </div>

                            <div class="mt-2">
                                <!-- Price -->
                                <p class="font-body text-sm font-bold text-red-600">
                                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                                </p>
                                <p class="font-body text-[10px] text-neutral-400 line-through">
                                    Rp{{ number_format($originalPrice, 0, ',', '.') }}
                                </p>

                                <!-- Tokopedia Progress Bar -->
                                <div class="mt-2">
                                    <div class="w-full bg-red-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: 75%"></div>
                                    </div>
                                    <p class="text-[10px] text-red-600 font-semibold mt-1">Segera Habis</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 4. Official Store Value Pillars (Tokopedia Keunggulan Belanja) -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4" data-aos="fade-up" data-aos-duration="600">
            <div class="p-4 rounded-xl bg-white border border-neutral-200 shadow-1 flex items-center gap-3" data-aos="fade-up" data-aos-delay="50">
                <div class="w-10 h-10 rounded-lg bg-gold-100 text-gold-800 flex items-center justify-center text-xl shrink-0">
                    🛡️
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900">100% Kriya Orisinal</h4>
                    <p class="font-body text-[11px] text-neutral-500">Karya pengrajin sejak 2004</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-neutral-200 shadow-1 flex items-center gap-3" data-aos="fade-up" data-aos-delay="100">
                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xl shrink-0">
                    ⚡
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900">Langsung Pengrajin</h4>
                    <p class="font-body text-[11px] text-neutral-500">Harga tangan pertama terbaik</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-neutral-200 shadow-1 flex items-center gap-3" data-aos="fade-up" data-aos-delay="150">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                    🚚
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900">Tarif Ongkir Transparan</h4>
                    <p class="font-body text-[11px] text-neutral-500">Kalkulasi otomatis per zona</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-neutral-200 shadow-1 flex items-center gap-3" data-aos="fade-up" data-aos-delay="200">
                <div class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xl shrink-0">
                    💬
                </div>
                <div>
                    <h4 class="font-body text-xs sm:text-sm font-bold text-neutral-900">Respon WhatsApp Cepat</h4>
                    <p class="font-body text-[11px] text-neutral-500">Konsultasi ukuran & partai besar</p>
                </div>
            </div>
        </section>

        <!-- 5. Rekomendasi Untuk Anda (Tokopedia Tabbed Product Feed) -->
        <section x-data="{ currentTab: 'all' }" class="space-y-6" data-aos="fade-up" data-aos-duration="600">
            <!-- Header & Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200 pb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-5 bg-gold-500 rounded-full"></span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-900">Rekomendasi Untuk Anda</h2>
                    </div>
                    <p class="font-body text-xs text-neutral-500 mt-0.5">Koleksi kriya pilihan pengrajin dengan kualitas bahan dan jahitan terbaik</p>
                </div>

                <!-- Tokopedia Style Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button @click="currentTab = 'all'" 
                            :class="currentTab === 'all' ? 'bg-primary-700 text-white font-bold shadow-sm' : 'bg-canvas-sunken text-neutral-600 hover:bg-neutral-200'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition">
                        Semua Kategori
                    </button>
                    <button @click="currentTab = 'songkok'" 
                            :class="currentTab === 'songkok' ? 'bg-primary-700 text-white font-bold shadow-sm' : 'bg-canvas-sunken text-neutral-600 hover:bg-neutral-200'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition">
                        👑 Songkok Nusantara
                    </button>
                    <button @click="currentTab = 'sarung'" 
                            :class="currentTab === 'sarung' ? 'bg-primary-700 text-white font-bold shadow-sm' : 'bg-canvas-sunken text-neutral-600 hover:bg-neutral-200'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition">
                        🧵 Sarung ATBM
                    </button>
                    <button @click="currentTab = 'jilbab'" 
                            :class="currentTab === 'jilbab' ? 'bg-primary-700 text-white font-bold shadow-sm' : 'bg-canvas-sunken text-neutral-600 hover:bg-neutral-200'" 
                            class="px-4 py-2 rounded-full font-body text-xs font-semibold whitespace-nowrap transition">
                        🧕 Jilbab Sekolah
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
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-primary-700 text-primary-700 hover:bg-primary-700 hover:text-white font-body text-xs sm:text-sm font-bold transition shadow-sm">
                    <span>Lihat Seluruh Koleksi Katalog ({{ $featuredProducts->count() }}+ Produk)</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
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
                    <a href="{{ route('partnership.index', ['tab' => 'grosir']) }}" class="px-5 py-2.5 rounded-lg bg-gold-500 hover:bg-gold-400 text-neutral-900 font-body text-xs sm:text-sm font-bold shadow-md transition text-center">
                        Ajukan Permohonan Grosir
                    </a>
                    <a href="{{ route('partnership.index', ['tab' => 'mitra']) }}" class="px-5 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white border border-white/30 font-body text-xs sm:text-sm font-semibold transition text-center">
                        Daftar Toko Mitra
                    </a>
                </div>
            </div>

            <!-- Background Decorative Accent -->
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full border-8 border-gold-500/15 pointer-events-none"></div>
        </section>

        <!-- 7. Testimoni & Ulasan Pembeli (Tokopedia Review Cards) -->
        <section class="bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-6" data-aos="fade-up" data-aos-duration="600">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-5 bg-gold-500 rounded-full"></span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-900">Ulasan & Kepercayaan Pelanggan</h2>
                    </div>
                    <p class="font-body text-xs text-neutral-500 mt-0.5">Pengalaman langsung dari pemesan retail dan mitra instansi pendidikan</p>
                </div>
                <div class="flex items-center gap-1 text-gold-500 font-bold text-sm">
                    <span>★ 5.0</span>
                    <span class="text-xs text-neutral-400 font-normal">(98% Puas)</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Testi 1 -->
                <div class="p-4 rounded-xl bg-canvas-sunken/60 border border-neutral-200/80 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                    <div>
                        <div class="flex items-center gap-1 text-gold-500 text-xs mb-2">
                            ★★★★★
                        </div>
                        <p class="font-body text-xs text-neutral-700 italic leading-relaxed">
                            "Songkok beludru halus RHS HERITAGE sangat nyaman di kepala, kokoh dan tidak mudah penyok meskipun dipakai santri setiap hari. Sudah langganan 3 tahun berturut-turut."
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-neutral-200 flex items-center justify-between">
                        <div>
                            <p class="font-body text-xs font-bold text-neutral-800">Ust. Ahmad Fauzi</p>
                            <p class="text-[10px] text-neutral-500">Pondok Pesantren Al-Falah, Jatim</p>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Terverifikasi</span>
                    </div>
                </div>

                <!-- Testi 2 -->
                <div class="p-4 rounded-xl bg-canvas-sunken/60 border border-neutral-200/80 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="200">
                    <div>
                        <div class="flex items-center gap-1 text-gold-500 text-xs mb-2">
                            ★★★★★
                        </div>
                        <p class="font-body text-xs text-neutral-700 italic leading-relaxed">
                            "Sarung tenun ATBM-nya luar biasa halus dan motifnya otentik khas tradisi. Sangat elegan untuk dipakai salat berjamaah maupun acara resmi keagamaan."
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-neutral-200 flex items-center justify-between">
                        <div>
                            <p class="font-body text-xs font-bold text-neutral-800">H. Rahmat Hidayat</p>
                            <p class="text-[10px] text-neutral-500">Pembeli Retail, Surabaya</p>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Terverifikasi</span>
                    </div>
                </div>

                <!-- Testi 3 -->
                <div class="p-4 rounded-xl bg-canvas-sunken/60 border border-neutral-200/80 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="300">
                    <div>
                        <div class="flex items-center gap-1 text-gold-500 text-xs mb-2">
                            ★★★★★
                        </div>
                        <p class="font-body text-xs text-neutral-700 italic leading-relaxed">
                            "Pesan jilbab seragam sekolah untuk 200 siswi, pengerjaan tepat waktu, pet rapi di dahi dan bahan adem tidak menerawang. Pelayanan owner via WA sangat komunikatif."
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-neutral-200 flex items-center justify-between">
                        <div>
                            <p class="font-body text-xs font-bold text-neutral-800">Ibu Nur Laili</p>
                            <p class="text-[10px] text-neutral-500">Yayasan Madrasah Ibtidaiyah</p>
                        </div>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Terverifikasi</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Tokopedia Style SEO & Heritage Story Box -->
        <section class="bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-4" data-aos="fade-up" data-aos-duration="600">
            <h3 class="font-display text-lg font-bold text-neutral-900">
                Pusat Kriya Songkok Nasional, Sarung ATBM & Seragam Jilbab Berkualitas — RHS HERITAGE
            </h3>
            <div class="font-body text-xs text-neutral-600 leading-relaxed space-y-3">
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
