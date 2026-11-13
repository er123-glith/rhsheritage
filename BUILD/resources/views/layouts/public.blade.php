<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Katalog Kriya Pilihan' }} - RHS HERITAGE</title>

    <!-- Google Fonts — Modern Apple / Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-full font-body text-neutral-700 bg-canvas-base antialiased flex flex-col pb-20 md:pb-0"
    x-data="{ mobileMenuOpen: false, searchOpen: false }"
    @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
    @keydown.escape.window="mobileMenuOpen = false; searchOpen = false"
>
    <!-- Top Announcement Bar -->
    <div class="bg-primary-900 text-gold-200 text-xs py-2 px-4 text-center font-medium border-b border-primary-800 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p>Kriya Tradisi Keluarga Sejak 2004 Menerima Pesanan Retail, Grosir & Kemitraan Sekolah</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('order.track') }}" class="text-primary-100 hover:text-white transition">Lacak Pesanan</a>
                <span class="text-primary-700">|</span>
                <a href="{{ route('partnership.index') }}" class="text-gold-400 hover:underline font-semibold">Harga Grosir & Kemitraan</a>
                <span class="text-primary-700">|</span>
                <a href="{{ route('admin.login') }}" class="text-primary-300 hover:text-white transition">Masuk Owner</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-neutral-200 sticky top-0 z-30 shadow-1 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-20 flex items-center justify-between gap-4">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                    <div class="w-11 h-11 rounded-full bg-primary-700 text-gold-400 font-display font-bold text-lg flex items-center justify-center border-2 border-gold-500 shadow-sm group-hover:scale-105 transition-transform">
                        RHS
                    </div>
                    <div>
                        <span class="font-display text-xl font-bold tracking-wide text-primary-700 block leading-tight group-hover:text-primary-800 transition-colors">
                            RHS HERITAGE
                        </span>
                        <span class="font-body text-[10px] uppercase tracking-widest text-gold-700 font-semibold block">
                            Kriya Tradisi Sejak 2004
                        </span>
                    </div>
                </a>

                <!-- Desktop Search Pill -->
                <form action="{{ route('catalog.index') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-6">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari songkok, sarung ATBM, jilbab..."
                            class="w-full h-11 pl-10 pr-4 rounded-pill bg-canvas-sunken text-sm text-neutral-800 placeholder-neutral-500 border-0 focus:ring-2 focus:ring-primary-500 focus:bg-white transition"
                        >
                    </div>
                </form>

                <!-- Desktop Navigation Links & Action Buttons -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Nav Links for Desktop -->
                    <nav class="hidden md:flex items-center gap-1 font-body text-sm font-semibold text-neutral-700">
                        <a href="{{ route('home') }}" class="px-3 py-2 text-white bg-primary-700 rounded-md hover:text-primary-700 transition-colors {{ request()->routeIs('home') ? 'text-primary-700 font-bold'  : '' }}">
                            Beranda
                        </a>
                        <a href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-md hover:text-primary-700 transition-colors {{ request()->routeIs('catalog.*') ? 'text-primary-700 font-bold' : '' }}">
                            Katalog
                        </a>
                        <a href="{{ route('about') }}" class="px-3 py-2 rounded-md hover:text-primary-700 transition-colors {{ request()->routeIs('about') ? 'text-primary-700 font-bold' : '' }}">
                            Tentang
                        </a>
                        <!-- Highlighted Grosir & Mitra button with Gold accent -->
                        <a
                            href="{{ route('partnership.index') }}"
                            class="px-3.5 py-1.5 ml-1 rounded-full bg-gold-100 hover:bg-gold-200 text-gold-900 border border-gold-400/60 font-semibold text-xs tracking-wide transition flex items-center gap-1.5 shadow-sm"
                        >
                            <span class="w-2 h-2 rounded-full bg-gold-600 animate-pulse"></span>
                            Grosir & Mitra
                        </a>
                    </nav>

                    <!-- Cart Icon Button -->
                    <a
                        href="{{ route('cart.index') }}"
                        class="relative p-2.5 rounded-full text-neutral-700 hover:text-primary-700 hover:bg-neutral-100 transition focus-visible:outline-primary-500"
                        aria-label="Lihat Keranjang Belanja"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute top-0.5 right-0.5 min-w-[20px] h-[20px] px-1 rounded-full bg-danger text-white font-body text-[11px] font-bold flex items-center justify-center ring-2 ring-white">
                                {{ ($cartCount ?? 0) > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Mobile Menu Hamburger Button -->
                    <button
                        type="button"
                        @click="mobileMenuOpen = true"
                        class="md:hidden p-2 rounded-lg text-neutral-700 hover:text-primary-700 hover:bg-neutral-100 focus-visible:outline-primary-500"
                        aria-label="Buka Menu Navigasi"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="lg:hidden pb-3 pt-1">
                <form action="{{ route('catalog.index') }}" method="GET" class="w-full">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari songkok, sarung, jilbab..."
                            class="w-full h-10 pl-10 pr-4 rounded-pill bg-canvas-sunken text-xs text-neutral-800 placeholder-neutral-500 border-0 focus:ring-2 focus:ring-primary-500 focus:bg-white transition"
                        >
                    </div>
                </form>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer / Off-canvas -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-hidden md:hidden"
        aria-modal="true"
        role="dialog"
    >
        <!-- Backdrop -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Drawer Panel -->
        <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-3 flex flex-col z-50">
            <!-- Drawer Header -->
            <div class="p-4 border-b border-neutral-200 flex items-center justify-between bg-primary-700 text-white">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white text-primary-700 font-display font-bold text-sm flex items-center justify-center">
                        RHS
                    </div>
                    <span class="font-display font-bold text-sm tracking-wide">RHS HERITAGE</span>
                </div>
                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="p-1 rounded-md text-white/80 hover:text-white"
                    aria-label="Tutup Menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Drawer Links -->
            <nav class="p-4 space-y-1 overflow-y-auto flex-1 font-body text-sm font-semibold">
                <a
                    href="{{ route('home') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-neutral-100 transition {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : 'text-neutral-700' }}"
                >
                    Beranda
                </a>
                <a
                    href="{{ route('catalog.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-neutral-100 transition {{ request()->routeIs('catalog.*') ? 'bg-primary-50 text-primary-700' : 'text-neutral-700' }}"
                >
                    Semua Katalog Produk
                </a>

                <div class="pt-2 pb-1 pl-3 text-[11px] font-bold uppercase tracking-wider text-neutral-400">
                    Kategori Kriya
                </div>
                <a href="{{ route('catalog.category', 'songkok') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-neutral-600 hover:bg-neutral-100 text-sm">
                    Songkok Nusantara
                </a>
                <a href="{{ route('catalog.category', 'sarung') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-neutral-600 hover:bg-neutral-100 text-sm">
                    Sarung ATBM Tradisi
                </a>
                <a href="{{ route('catalog.category', 'jilbab') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-neutral-600 hover:bg-neutral-100 text-sm">
                    Jilbab & Kerudung Sekolah
                </a>

                <div class="pt-3 border-t border-neutral-200 mt-2 space-y-2">
                    <a
                        href="{{ route('partnership.index') }}"
                        class="flex items-center justify-between px-3.5 py-3 rounded-lg bg-gold-100 text-gold-900 border border-gold-300 font-bold transition shadow-sm"
                    >
                        <span>Harga Grosir & Kemitraan</span>
                        <span class="text-xs">&rarr;</span>
                    </a>

                    <a href="{{ route('order.track') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-neutral-700 hover:bg-neutral-100">
                        Lacak Status Pesanan
                    </a>
                    <a href="{{ route('about') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-neutral-700 hover:bg-neutral-100">
                        Tentang RHS HERITAGE
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-neutral-200 bg-neutral-50 text-xs text-neutral-500">
                <a href="{{ route('admin.login') }}" class="text-primary-700 font-semibold hover:underline block mb-1">
                    Masuk Panel Owner &rarr;
                </a>
                <p>&copy; {{ date('Y') }} RHS HERITAGE</p>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 Flash Alerts -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.SwalToast) {
                    window.SwalToast.fire({
                        icon: 'success',
                        title: @json(session('success'))
                    });
                } else if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: @json(session('success')),
                        confirmButtonColor: '#0F3D2E',
                        customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                    });
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Perhatian',
                        text: @json(session('error')),
                        confirmButtonColor: '#C0392B',
                        customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                    });
                }
            });
        </script>
    @endif


    <!-- Main Content Area -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation -->
    <x-bottom-nav :cartCount="$cartCount ?? 0" />

    <!-- Footer -->
    <footer class="bg-primary-900 text-primary-100 pt-16 pb-12 mt-auto border-t border-primary-800" data-aos="fade-up" data-aos-duration="650">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Brand Column -->
                <div class="md:col-span-1 space-y-4" data-aos="fade-up" data-aos-delay="50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-800 text-gold-400 font-display font-bold flex items-center justify-center border border-gold-500">
                            RHS
                        </div>
                        <div>
                            <p class="font-display text-lg font-bold text-white">RHS HERITAGE</p>
                            <p class="font-body text-[10px] uppercase tracking-widest text-gold-400 font-semibold">Tradisi Kriya Sejak 2004</p>
                        </div>
                    </div>
                    <p class="font-body text-xs text-primary-300 leading-relaxed">
                        Pusat produksi dan distribusi Songkok Nasional, Sarung Tenun Bukan Mesin (ATBM), dan Jilbab Seragam Sekolah berkualitas dengan jahitan presisi.
                    </p>
                </div>

                <!-- Produk Links -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h4 class="font-display text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">
                        Koleksi Kriya
                    </h4>
                    <ul class="space-y-2 text-xs font-body text-primary-200">
                        <li><a href="{{ route('catalog.category', 'songkok') }}" class="hover:text-white transition">Songkok Nusantara</a></li>
                        <li><a href="{{ route('catalog.category', 'sarung') }}" class="hover:text-white transition">Sarung ATBM Sutra & Goyor</a></li>
                        <li><a href="{{ route('catalog.category', 'jilbab') }}" class="hover:text-white transition">Jilbab & Kerudung Sekolah</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition">Semua Produk Katalog</a></li>
                    </ul>
                </div>

                <!-- Layanan & Kemitraan -->
                <div data-aos="fade-up" data-aos-delay="150">
                    <h4 class="font-display text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">
                        Layanan Pelanggan
                    </h4>
                    <ul class="space-y-2 text-xs font-body text-primary-200">
                        <li><a href="{{ route('order.track') }}" class="hover:text-white transition">Lacak Status Pesanan</a></li>
                        <li><a href="{{ route('partnership.index', ['tab' => 'grosir']) }}" class="hover:text-white transition">Harga Grosir Pesantren & Yayasan</a></li>
                        <li><a href="{{ route('partnership.index', ['tab' => 'mitra']) }}" class="hover:text-white transition">Peluang Toko Mitra Resmi</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Profil Usaha & Workshop</a></li>
                    </ul>
                </div>

                <!-- Kontak & Jam Kerja -->
                <div data-aos="fade-up" data-aos-delay="200">
                    <h4 class="font-display text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">
                        Pemesanan Langsung
                    </h4>
                    <p class="font-body text-xs text-primary-300 leading-relaxed mb-3">
                        Pemesanan eceran dan konfirmasi order langsung diproses melalui WhatsApp resmi owner.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primary-800/80 border border-primary-700 text-xs text-primary-200">
                        <span class="w-2 h-2 rounded-full bg-success animate-ping"></span>
                        <span>Respon Cepat Jam Kerja</span>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-primary-800/80 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-body text-primary-400">
                <p>&copy; {{ date('Y') }} RHS HERITAGE. Seluruh Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('about') }}" class="hover:text-primary-200 transition">Tentang Kami</a>
                    <span>&bull;</span>
                    <a href="{{ route('admin.login') }}" class="text-gold-500 hover:text-gold-400 transition font-semibold">Panel Owner</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>