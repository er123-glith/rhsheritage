<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Katalog Kriya Pilihan' }} - RHS HERITAGE</title>

    <!-- Google Fonts — Modern Apple / Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Theme Init & Global Toggle Script (Instant Execution) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        window.__toggleTheme = function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (window.Alpine && Alpine.store('theme')) {
                Alpine.store('theme').dark = isDark;
            }
        };
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $cartCount = app(\App\Services\CartService::class)->count();
@endphp
<body
    class="min-h-full font-body text-neutral-700 bg-canvas-base antialiased flex flex-col pb-20 md:pb-0 transition-colors duration-200"
    x-data="{ mobileMenuOpen: false, searchOpen: false }"
    @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
    @keydown.escape.window="mobileMenuOpen = false; searchOpen = false"
>
    <!-- Top Announcement Bar (Desktop) -->
    <div class="bg-primary-900 text-gold-200 text-xs py-2 px-4 text-center font-medium border-b border-primary-800 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p>Kriya Tradisi Keluarga Sejak 2004 — Menerima Pesanan Retail, Grosir & Kemitraan Sekolah</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('order.track') }}" class="text-primary-100 hover:text-white transition">Lacak Pesanan</a>
                <span class="text-primary-700">|</span>
                <a href="{{ route('partnership.index') }}" class="text-gold-400 hover:underline font-semibold">Harga Grosir & Kemitraan</a>
                <span class="text-primary-700">|</span>
                @guest
                    <a href="{{ route('login') }}" class="text-primary-200 hover:text-white transition font-medium">Masuk</a>
                    <span class="text-primary-700">|</span>
                    <a href="{{ route('register') }}" class="text-gold-300 hover:text-white transition font-semibold">Daftar</a>
                @endguest
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gold-300 hover:text-white transition font-bold flex items-center gap-1">
                            <span>👑</span> Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('customer.orders') }}" class="text-primary-100 hover:text-white transition">Pesanan Saya</a>
                        <span class="text-primary-700">|</span>
                        <a href="{{ route('profile.edit') }}" class="text-gold-300 hover:text-white transition">Profil ({{ Str::limit(auth()->user()->name, 15) }})</a>
                    @endif
                    <span class="text-primary-700">|</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-rose-300 hover:text-rose-100 transition cursor-pointer text-xs">Keluar</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white/95 dark:bg-[#14201B]/95 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-30 shadow-1 transition-all">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="h-16 sm:h-20 flex items-center justify-between gap-3 sm:gap-4">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 shrink-0 group">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-primary-700 text-gold-400 font-display font-bold text-base sm:text-lg flex items-center justify-center border-2 border-gold-500 shadow-sm group-hover:scale-105 transition-transform">
                        RHS
                    </div>
                    <div>
                        <span class="font-display text-base sm:text-xl font-bold tracking-tight text-primary-700 dark:text-gold-400 block leading-tight group-hover:text-primary-800 transition-colors">
                            RHS HERITAGE
                        </span>
                        <span class="font-body text-[8px] sm:text-[10px] uppercase tracking-widest text-gold-700 dark:text-neutral-400 font-semibold block">
                            Kriya Tradisi 2004
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

                <!-- Navigation Links & Action Buttons -->
                <div class="flex items-center gap-1 sm:gap-3">
                    <!-- Nav Links for Desktop -->
                    <nav class="hidden md:flex items-center gap-1 font-body text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                        <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg hover:text-primary-700 dark:hover:text-gold-400 transition-colors {{ request()->routeIs('home') ? 'text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                            Beranda
                        </a>
                        <a href="{{ route('catalog.index') }}" class="px-3 py-2 rounded-lg hover:text-primary-700 dark:hover:text-gold-400 transition-colors {{ request()->routeIs('catalog.*') ? 'text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                            Katalog
                        </a>

                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg bg-gold-100 dark:bg-gold-950/60 text-gold-900 dark:text-gold-300 border border-gold-300/80 font-bold text-xs hover:bg-gold-200 transition">
                                    Panel Admin
                                </a>
                            @else
                                <a href="{{ route('customer.orders') }}" class="px-3 py-2 rounded-lg hover:text-primary-700 dark:hover:text-gold-400 transition-colors {{ request()->routeIs('customer.orders') ? 'text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                                    Pesanan Saya
                                </a>
                                <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-lg hover:text-primary-700 dark:hover:text-gold-400 transition-colors {{ request()->routeIs('profile.*') ? 'text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                                    Profil
                                </a>
                            @endif
                        @endauth

                        <a href="{{ route('about') }}" class="px-3 py-2 rounded-lg hover:text-primary-700 dark:hover:text-gold-400 transition-colors {{ request()->routeIs('about') ? 'text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                            Tentang
                        </a>

                        <!-- Highlighted Grosir & Mitra button -->
                        <a
                            href="{{ route('partnership.index') }}"
                            class="px-3.5 py-1.5 ml-1 rounded-full bg-gold-100 hover:bg-gold-200 text-gold-900 border border-gold-400/60 font-semibold text-xs tracking-wide transition flex items-center gap-1.5 shadow-sm"
                        >
                            <span class="w-2 h-2 rounded-full bg-gold-600 animate-pulse"></span>
                            Grosir & Mitra
                        </a>

                        @guest
                            <div class="flex items-center gap-1.5 ml-2 pl-2 border-l border-neutral-200 dark:border-neutral-700">
                                <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-primary-700 dark:text-gold-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-primary-700 hover:bg-primary-800 text-white shadow-xs transition">
                                    Daftar
                                </a>
                            </div>
                        @endguest

                        @auth
                            <div class="ml-2 pl-2 border-l border-neutral-200 dark:border-neutral-700">
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </nav>

                    <!-- Theme Toggle Button in Top Right Navbar -->
                    <x-theme-toggle />

                    <!-- Cart Icon Button -->
                    <a
                        href="{{ route('cart.index') }}"
                        class="relative p-2 sm:p-2.5 rounded-full text-neutral-700 dark:text-neutral-300 hover:text-primary-700 dark:hover:text-gold-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition focus-visible:outline-primary-500"
                        aria-label="Lihat Keranjang Belanja"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute top-0 right-0 min-w-[18px] h-[18px] px-1 rounded-full bg-danger text-white font-body text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-neutral-900">
                                {{ $cartCount > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Mobile Menu Hamburger Button -->
                    <button
                        type="button"
                        @click="mobileMenuOpen = true"
                        class="md:hidden p-2 rounded-lg text-neutral-700 dark:text-neutral-300 hover:text-primary-700 dark:hover:text-gold-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 focus-visible:outline-primary-500"
                        aria-label="Buka Menu Navigasi"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Bar (Below Header on Small Screens) -->
            <div class="lg:hidden pb-3 pt-0.5">
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
                            class="w-full h-10 pl-10 pr-4 rounded-full bg-canvas-sunken text-xs text-neutral-800 placeholder-neutral-500 border-0 focus:ring-2 focus:ring-primary-500 focus:bg-white transition"
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
            class="fixed inset-0 bg-neutral-900/70 backdrop-blur-sm"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Drawer Panel -->
        <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-white dark:bg-[#14201B] shadow-3 flex flex-col z-50 transition-colors">
            <!-- Drawer Header -->
            <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-primary-700 text-white">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white text-primary-700 font-display font-bold text-sm flex items-center justify-center">
                        RHS
                    </div>
                    <span class="font-display font-bold text-sm tracking-wide">RHS HERITAGE</span>
                </div>
                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10"
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
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition {{ request()->routeIs('home') ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-gold-400' : 'text-neutral-700 dark:text-neutral-200' }}"
                >
                    <span class="text-base">🏠</span>
                    <span>Beranda</span>
                </a>
                <a
                    href="{{ route('catalog.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition {{ request()->routeIs('catalog.*') ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-gold-400' : 'text-neutral-700 dark:text-neutral-200' }}"
                >
                    <span class="text-base">🛍️</span>
                    <span>Semua Katalog Produk</span>
                </a>

                <div class="pt-3 pb-1 pl-3 text-[11px] font-bold uppercase tracking-wider text-neutral-400">
                    Kategori Kriya
                </div>
                <a href="{{ route('catalog.category', 'songkok') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-sm">
                    <span>👑</span>
                    <span>Songkok Nusantara</span>
                </a>
                <a href="{{ route('catalog.category', 'sarung') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-sm">
                    <span>🧵</span>
                    <span>Sarung ATBM Tradisi</span>
                </a>
                <a href="{{ route('catalog.category', 'jilbab') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-sm">
                    <span>🧕</span>
                    <span>Jilbab & Kerudung Sekolah</span>
                </a>

                <div class="pt-3 border-t border-neutral-200 dark:border-neutral-700 mt-2 space-y-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="flex items-center justify-between px-3.5 py-3 rounded-xl bg-gold-100 dark:bg-gold-950/60 text-gold-900 dark:text-gold-300 border border-gold-300 font-bold transition shadow-sm"
                            >
                                <span>👑 Panel Dashboard Admin</span>
                                <span class="text-xs">&rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ request()->routeIs('customer.orders') ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                                <span>📦 Pesanan Saya</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 {{ request()->routeIs('profile.*') ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-gold-400 font-bold' : '' }}">
                                <span>👤 Profil Saya</span>
                            </a>
                        @endif
                    @endauth

                    <a
                        href="{{ route('partnership.index') }}"
                        class="flex items-center justify-between px-3.5 py-3 rounded-xl bg-gold-50 dark:bg-neutral-800 text-gold-900 dark:text-gold-300 border border-gold-300/60 font-bold transition shadow-sm"
                    >
                        <span>📦 Harga Grosir & Mitra</span>
                        <span class="text-xs">&rarr;</span>
                    </a>

                    <a href="{{ route('order.track') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                        <span>🔍 Lacak Status Pesanan</span>
                    </a>

                    <!-- Theme Toggle in Mobile Drawer -->
                    <div class="pt-1">
                        <x-theme-toggle variant="pill" class="w-full justify-start px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-canvas-sunken" />
                    </div>

                    <a href="{{ route('about') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                        <span>🏛️ Tentang RHS HERITAGE</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900/50 text-xs text-neutral-500">
                @guest
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <a href="{{ route('login') }}" class="py-2.5 text-center rounded-lg border border-primary-700 text-primary-700 dark:text-gold-400 font-bold hover:bg-primary-50 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="py-2.5 text-center rounded-lg bg-primary-700 text-white font-bold hover:bg-primary-800 transition shadow-xs">
                            Daftar
                        </a>
                    </div>
                @endguest

                @auth
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-neutral-200 dark:border-neutral-700">
                        <div>
                            <span class="font-bold text-neutral-800 dark:text-white block">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-neutral-400 uppercase tracking-wider">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Pelanggan' }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-rose-600 font-bold hover:underline cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    </div>
                @endauth

                <p class="text-center text-[11px] text-neutral-400">&copy; {{ date('Y') }} RHS HERITAGE</p>
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


    <!-- Main Page Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-primary-900 text-white mt-12 sm:mt-20 border-t border-primary-800 pb-20 md:pb-0" data-aos="fade-up" data-aos-duration="650">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <!-- Brand Info -->
                <div class="md:col-span-1 space-y-4" data-aos="fade-up" data-aos-delay="50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-800 text-gold-400 font-display font-bold text-lg flex items-center justify-center border-2 border-gold-500">
                            RHS
                        </div>
                        <span class="font-display text-xl font-bold tracking-wide text-white">
                            RHS HERITAGE
                        </span>
                    </div>
                    <p class="font-body text-xs text-primary-200 leading-relaxed">
                        Spesialis kriya Songkok Nasional, Sarung ATBM (Alat Tenun Bukan Mesin) eksklusif, serta Jilbab Seragam Sekolah sejak 2004.
                    </p>
                </div>

                <!-- Kategori Cepat -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h4 class="font-display text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">
                        Kategori Kriya
                    </h4>
                    <ul class="space-y-2 text-xs font-body text-primary-200">
                        <li><a href="{{ route('catalog.category', 'songkok') }}" class="hover:text-white transition">Songkok Nusantara</a></li>
                        <li><a href="{{ route('catalog.category', 'sarung') }}" class="hover:text-white transition">Sarung ATBM Tradisi</a></li>
                        <li><a href="{{ route('catalog.category', 'jilbab') }}" class="hover:text-white transition">Jilbab & Kerudung Sekolah</a></li>
                        <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition">Seluruh Katalog</a></li>
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
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-primary-800/80 border border-primary-700 text-xs text-primary-200">
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

    <!-- Mobile Floating Bottom Navigation Bar -->
    <x-bottom-nav :cart-count="$cartCount" />
</body>
</html>