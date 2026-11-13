<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — Panel Owner RHS HERITAGE' : 'Panel Owner — RHS HERITAGE' }}</title>

    <!-- Favicon RHS Heritage Resmi -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

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

        window.openAdminProfileModal = function() {
            window.dispatchEvent(new CustomEvent('open-profile-modal'));
        };
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-body text-neutral-700 bg-canvas-base antialiased" x-data="{ sidebarOpen: false, profileModalOpen: false }" @open-profile-modal.window="profileModalOpen = true">
    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Sidebar Backdrop for Mobile -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-neutral-900/60 backdrop-blur-xs md:hidden"
        ></div>

        <!-- Sidebar Navigation -->
        @php
            $sidebarPendingOrders = \App\Models\Order::where('status', \App\Enums\OrderStatus::MenungguKonfirmasi)->count();
            $sidebarPendingPartners = \App\Models\PartnerApplication::where('is_followed_up', false)->count();
            $sidebarPendingReturns = \App\Models\OrderReturn::where('status', \App\Enums\ReturnStatus::MenungguReview)->count();
            $sidebarPendingReviews = \App\Models\Review::where('moderation_status', \App\Models\Review::STATUS_PENDING_REVIEW)->count();
        @endphp
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-72 h-screen bg-primary-700 text-white flex flex-col transition-transform duration-200 ease-in-out shrink-0 shadow-3 md:shadow-md"
        >
            <!-- Brand / Header -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-primary-600">
                <div>
                    <span class="font-display text-xl font-bold tracking-wide text-gold-400">RHS HERITAGE</span>
                    <span class="block font-body text-xs text-primary-200 tracking-wider">PANEL OWNER</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-primary-200 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md font-body text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 font-body text-[10px] font-bold uppercase tracking-widest text-primary-300">Katalog & Stok</p>
                </div>

                <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.variants.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Produk & Varian</span>
                    </span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori</span>
                    </span>
                </a>

                <a href="{{ route('admin.stock.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.stock.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Kelola Stok</span>
                    </span>
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 font-body text-[10px] font-bold uppercase tracking-widest text-primary-300">Transaksi & Mitra</p>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Pesanan Masuk</span>
                    </span>
                    @if($sidebarPendingOrders > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gold-500 text-neutral-900 animate-pulse">
                            {{ $sidebarPendingOrders }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.returns.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.returns.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        <span>Manajemen Retur</span>
                    </span>
                    @if($sidebarPendingReturns > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-neutral-900 animate-pulse">
                            {{ $sidebarPendingReturns }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.reviews.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-star text-sm w-5 text-center"></i>
                        <span>Manajemen Ulasan</span>
                    </span>
                    @if($sidebarPendingReviews > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-neutral-900 animate-pulse">
                            {{ $sidebarPendingReviews }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.customers.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.customers.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Data Pelanggan</span>
                    </span>
                </a>

                <a href="{{ route('admin.partners.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.partners.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Grosir & Mitra</span>
                    </span>
                    @if($sidebarPendingPartners > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gold-500 text-neutral-900">
                            {{ $sidebarPendingPartners }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.shipping.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.shipping.*') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500 font-semibold' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <span>Zona Ongkir</span>
                    </span>
                </a>

                <button 
                    type="button"
                    @click="profileModalOpen = true" 
                    onclick="if(window.openAdminProfileModal) window.openAdminProfileModal();"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition text-primary-100 hover:bg-primary-600 hover:text-white text-left cursor-pointer"
                >
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profil & Toko</span>
                    </span>
                </button>
            </nav>

            <!-- User / Logout Footer -->
            <div class="p-4 border-t border-primary-600 bg-primary-800/60">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <button 
                            type="button" 
                            @click="profileModalOpen = true" 
                            onclick="if(window.openAdminProfileModal) window.openAdminProfileModal();"
                            class="w-full text-left block group cursor-pointer p-1 -m-1 rounded-md hover:bg-primary-700/60 transition" 
                            title="Klik untuk melihat dan mengubah profil admin"
                        >
                            <p class="font-body text-sm font-semibold text-white group-hover:text-gold-300 transition truncate">{{ auth()->user()->name ?? 'Owner' }}</p>
                            <p class="font-body text-xs text-primary-200 group-hover:text-white transition truncate">{{ auth()->user()->email ?? '' }}</p>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-primary-200 hover:text-white hover:bg-primary-700 rounded-md transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 md:pl-72 min-h-screen">
            <!-- Mobile Topbar -->
            <header class="h-16 bg-white border-b border-neutral-200 px-4 flex items-center justify-between md:hidden shadow-1">
                <button @click="sidebarOpen = true" class="p-2 text-neutral-700 hover:bg-neutral-50 rounded-md">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-display font-bold text-primary-700">RHS HERITAGE</span>
                <button type="button" @click="profileModalOpen = true" class="p-2 text-neutral-600 hover:text-primary-700 rounded-md transition" title="Profil & Toko">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </button>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- SweetAlert2 Flash Alerts -->
                @if (session('status') || session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            if (window.SwalToast) {
                                window.SwalToast.fire({
                                    icon: 'success',
                                    title: @json(session('status') ?? session('success'))
                                });
                            } else if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: @json(session('status') ?? session('success')),
                                    confirmButtonColor: '#0F3D2E',
                                    customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                                });
                            }
                        });
                    </script>
                @endif

                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: @json(session('error')),
                                    confirmButtonColor: '#C0392B',
                                    customClass: { popup: 'rhs-swal-popup', title: 'rhs-swal-title', confirmButton: 'rhs-swal-confirm' }
                                });
                            }
                        });
                    </script>
                @endif


                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Modal Profil & Toko Admin RHS Heritage -->
    <div 
        x-show="profileModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-profile-title" 
        role="dialog" 
        aria-modal="true"
        @keydown.escape.window="profileModalOpen = false"
    >
        <!-- Backdrop -->
        <div 
            x-show="profileModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-neutral-900/60 backdrop-blur-xs transition-opacity" 
            @click="profileModalOpen = false"
        ></div>

        <!-- Dialog Container -->
        <div class="flex min-h-full items-end sm:items-center justify-center p-3 sm:p-4 text-center sm:p-0">
            <div 
                x-show="profileModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-neutral-200 my-6"
                x-data="{ tab: 'view' }"
            >
                <!-- Modal Header -->
                <div class="bg-primary-900 px-6 py-5 text-white flex items-center justify-between border-b border-primary-800">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gold-500/20 border border-gold-400/40 flex items-center justify-center text-gold-400 font-display font-bold text-lg">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-display font-bold text-lg text-white" id="modal-profile-title">
                                    {{ auth()->user()->name ?? 'Admin RHS Heritage' }}
                                </h3>
                                <span class="px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider rounded-full bg-gold-500 text-neutral-900">
                                    {{ auth()->user()->is_owner ? 'Owner' : 'Admin' }}
                                </span>
                            </div>
                            <p class="font-body text-xs text-primary-200 mt-0.5">
                                {{ auth()->user()->email ?? '' }} &bull; Pengelola Resmi RHS Heritage
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="profileModalOpen = false" 
                        class="text-primary-300 hover:text-white hover:bg-primary-800/80 p-2 rounded-lg transition"
                        title="Tutup dialog"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Tabs -->
                <div class="flex border-b border-neutral-200 bg-neutral-50 px-6">
                    <button 
                        type="button" 
                        @click="tab = 'view'"
                        :class="tab === 'view' ? 'border-primary-700 text-primary-800 font-bold bg-white' : 'border-transparent text-neutral-500 hover:text-neutral-700 font-medium'"
                        class="py-3 px-4 border-b-2 text-sm transition"
                    >
                        Ringkasan Profil & Toko
                    </button>
                    <button 
                        type="button" 
                        @click="tab = 'edit'"
                        :class="tab === 'edit' ? 'border-primary-700 text-primary-800 font-bold bg-white' : 'border-transparent text-neutral-500 hover:text-neutral-700 font-medium'"
                        class="py-3 px-4 border-b-2 text-sm transition"
                    >
                        Ubah Informasi & Alamat
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 max-h-[72vh] overflow-y-auto">
                    <!-- Tab 1: Ringkasan Profil (View Mode) -->
                    <div x-show="tab === 'view'" class="space-y-5">
                        <!-- Informasi Akun -->
                        <div>
                            <h4 class="font-display font-semibold text-xs tracking-wider uppercase text-neutral-500 mb-3">
                                Informasi Akun Admin
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-200">
                                    <span class="block text-xs font-medium text-neutral-400">Nama Lengkap</span>
                                    <span class="block text-sm font-semibold text-neutral-900 mt-0.5">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-200">
                                    <span class="block text-xs font-medium text-neutral-400">Alamat Email</span>
                                    <span class="block text-sm font-semibold text-neutral-900 mt-0.5">{{ auth()->user()->email }}</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-200">
                                    <span class="block text-xs font-medium text-neutral-400">Nomor WhatsApp / HP</span>
                                    <span class="block text-sm font-semibold text-neutral-900 mt-0.5">{{ auth()->user()->phone ?: 'Belum diatur' }}</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-neutral-50 border border-neutral-200">
                                    <span class="block text-xs font-medium text-neutral-400">Peran Sistem (Role)</span>
                                    <span class="block text-sm font-semibold text-primary-800 mt-0.5">
                                        {{ auth()->user()->is_owner ? 'Owner & Penanggung Jawab Toko' : 'Administrator' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Toko & KOP Invoice -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-display font-semibold text-xs tracking-wider uppercase text-neutral-500">
                                    Alamat Resmi Toko & KOP Invoice
                                </h4>
                                <span class="text-[11px] font-semibold text-gold-700 bg-gold-50 border border-gold-200 px-2 py-0.5 rounded-full">
                                    Otomatis Tercetak di Invoice A4
                                </span>
                            </div>

                            <div class="p-4 rounded-xl bg-primary-50/50 border border-primary-200/80">
                                <div class="space-y-2 text-sm text-neutral-800">
                                    <div>
                                        <span class="text-xs font-medium text-neutral-500 block">Alamat Galeri / Workshop:</span>
                                        <span class="font-medium text-neutral-900">{{ auth()->user()->address ?: 'Belum diisi (menggunakan default sistem)' }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 pt-1 border-t border-primary-200/60">
                                        <div>
                                            <span class="text-xs text-neutral-500 block">Kota / Kab:</span>
                                            <span class="font-semibold text-neutral-900">{{ auth()->user()->city ?: '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-neutral-500 block">Provinsi:</span>
                                            <span class="font-semibold text-neutral-900">{{ auth()->user()->province ?: '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-neutral-500 block">Kode Pos:</span>
                                            <span class="font-semibold text-neutral-900">{{ auth()->user()->postal_code ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions View Mode -->
                        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-neutral-200">
                            <a 
                                href="{{ route('admin.profile.edit') }}" 
                                class="text-xs font-semibold text-primary-700 hover:text-primary-900 underline transition"
                            >
                                Buka Pengaturan Keamanan & Password &rarr;
                            </a>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button 
                                    type="button" 
                                    @click="tab = 'edit'" 
                                    class="w-full sm:w-auto px-4 py-2 bg-primary-800 hover:bg-primary-900 text-white text-xs font-semibold rounded-lg shadow-sm transition"
                                >
                                    Ubah Profil & Alamat
                                </button>
                                <button 
                                    type="button" 
                                    @click="profileModalOpen = false" 
                                    class="w-full sm:w-auto px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-lg transition"
                                >
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Form Ubah Profil & Alamat (Edit Mode) -->
                    <div x-show="tab === 'edit'">
                        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_name">Nama Pengelola *</label>
                                    <input 
                                        id="modal_admin_name" 
                                        name="name" 
                                        type="text" 
                                        required 
                                        value="{{ old('name', auth()->user()->name) }}" 
                                        class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_phone">Nomor WhatsApp / HP</label>
                                    <input 
                                        id="modal_admin_phone" 
                                        name="phone" 
                                        type="text" 
                                        value="{{ old('phone', auth()->user()->phone) }}" 
                                        placeholder="081234567890" 
                                        class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_email">Alamat Email *</label>
                                <input 
                                    id="modal_admin_email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    value="{{ old('email', auth()->user()->email) }}" 
                                    class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                >
                            </div>

                            <div class="pt-2 border-t border-neutral-200">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-neutral-600 mb-2">
                                    Alamat Toko & Workshop (KOP Surat Invoice)
                                </h5>
                                <div>
                                    <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_address">Alamat Jalan & No. Bangunan</label>
                                    <textarea 
                                        id="modal_admin_address" 
                                        name="address" 
                                        rows="2" 
                                        placeholder="Contoh: Jl. Budaya Warisan No. 88, Griya RHS Heritage"
                                        class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                    >{{ old('address', auth()->user()->address) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_city">Kota / Kabupaten</label>
                                        <input 
                                            id="modal_admin_city" 
                                            name="city" 
                                            type="text" 
                                            placeholder="Surakarta" 
                                            value="{{ old('city', auth()->user()->city) }}" 
                                            class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_province">Provinsi</label>
                                        <input 
                                            id="modal_admin_province" 
                                            name="province" 
                                            type="text" 
                                            placeholder="Jawa Tengah" 
                                            value="{{ old('province', auth()->user()->province) }}" 
                                            class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-neutral-700 mb-1" for="modal_admin_postal">Kode Pos</label>
                                        <input 
                                            id="modal_admin_postal" 
                                            name="postal_code" 
                                            type="text" 
                                            placeholder="57126" 
                                            value="{{ old('postal_code', auth()->user()->postal_code) }}" 
                                            class="w-full px-3 py-2 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-primary-600"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Actions Edit Mode -->
                            <div class="pt-4 flex items-center justify-end gap-2 border-t border-neutral-200">
                                <button 
                                    type="button" 
                                    @click="tab = 'view'" 
                                    class="px-4 py-2 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-lg transition"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-5 py-2 bg-primary-800 hover:bg-primary-900 text-white text-xs font-semibold rounded-lg shadow-sm transition"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
