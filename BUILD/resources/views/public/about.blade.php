<x-layouts.public>
    <x-slot:title>Tentang RHS HERITAGE — Kriya Tradisi Sejak 2004</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-12">
        <!-- Breadcrumbs & Heading -->
        <div class="text-center space-y-3" data-aos="fade-down" data-aos-duration="600">
            <nav class="flex items-center justify-center gap-2 text-xs font-body text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                <span>/</span>
                <span class="text-primary-700 font-semibold">Tentang Kami</span>
            </nav>

            <span class="inline-block px-3.5 py-1 rounded-full bg-gold-100 text-gold-900 font-body text-xs font-bold uppercase tracking-wider border border-gold-300">
                Perjalanan Usaha Keluarga
            </span>

            <h1 class="font-display text-3xl sm:text-4xl font-bold text-neutral-800">
                {{ $content->title ?? 'Tentang RHS HERITAGE' }}
            </h1>
            <p class="font-body text-sm text-neutral-500 max-w-xl mx-auto">
                Dedikasi lebih dari dua dekade memproduksi kriya busana muslim tradisi nusantara berkualitas tinggi.
            </p>
        </div>

        <!-- Main Narrative Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-10 border border-neutral-200 shadow-1 space-y-6" data-aos="fade-up" data-aos-duration="700">
            <div class="prose prose-base text-neutral-700 font-body max-w-none leading-relaxed">
                {!! $content->body ?? '<p>RHS HERITAGE berdedikasi memproduksi Songkok Nasional, Sarung ATBM tradisi, dan Jilbab Sekolah berkualitas tinggi sejak tahun 2004.</p>' !!}
            </div>

            <!-- Pillars Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-8 border-t border-neutral-100">
                <div class="p-4 rounded-xl bg-canvas-sunken space-y-2 border border-neutral-200/80" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-10 h-10 rounded-lg bg-primary-700 text-gold-400 flex items-center justify-center font-display font-bold text-lg">
                        1
                    </div>
                    <h3 class="font-display font-bold text-neutral-800">Ketelitian Manual</h3>
                    <p class="font-body text-xs text-neutral-600 leading-relaxed">
                        Setiap songkok dibentuk teliti dengan beludru kualitas impor, presisi jahitan rapi tanpa kerutan.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-canvas-sunken space-y-2 border border-neutral-200/80" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-10 h-10 rounded-lg bg-primary-700 text-gold-400 flex items-center justify-center font-display font-bold text-lg">
                        2
                    </div>
                    <h3 class="font-display font-bold text-neutral-800">Tenun Asli ATBM</h3>
                    <p class="font-body text-xs text-neutral-600 leading-relaxed">
                        Sarung tenun bukan mesin yang dikerjakan oleh para perajin lokal dengan benang pilihan yang nyaman dipakai beribadah.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-canvas-sunken space-y-2 border border-neutral-200/80" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-10 h-10 rounded-lg bg-primary-700 text-gold-400 flex items-center justify-center font-display font-bold text-lg">
                        3
                    </div>
                    <h3 class="font-display font-bold text-neutral-800">Kenyamanan Anak Sekolah</h3>
                    <p class="font-body text-xs text-neutral-600 leading-relaxed">
                        Jilbab seragam sekolah dengan pola pet yang nyaman di dahi, bahan adem, dan awet dicuci setiap hari.
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact & Workshop Info -->
        <div class="rounded-2xl bg-gradient-to-r from-primary-800 to-primary-900 text-white p-8 sm:p-10 border border-gold-500/30 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-2" data-aos="zoom-in" data-aos-duration="700">
            <div class="space-y-2 text-center sm:text-left">
                <h3 class="font-display text-xl sm:text-2xl font-bold text-white">
                    Ingin Berkonsultasi Langsung?
                </h3>
                <p class="font-body text-xs sm:text-sm text-primary-100 max-w-md leading-relaxed">
                    Kami melayani pemesanan seragam madrasah, kustomisasi songkok berlogo bordir, dan penawaran kemitraan toko.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
                <x-button :href="route('partnership.index')" variant="gold" size="md">
                    Hubungi via Kemitraan
                </x-button>
                <x-button :href="route('catalog.index')" variant="outlined" size="md" class="text-white border-white/50 hover:bg-white/10">
                    Lihat Katalog Produk
                </x-button>
            </div>
        </div>
    </div>
</x-layouts.public>
