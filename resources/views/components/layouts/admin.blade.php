<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} � Panel Owner RHS HERITAGE</title>

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
<body class="h-full font-body text-neutral-700 bg-canvas-base antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Sidebar Backdrop for Mobile -->
        <div 
            x-show="sidebarOpen" 
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-neutral-900/60 backdrop-blur-xs md:hidden"
        ></div>

        <!-- Sidebar Navigation -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-primary-700 text-white flex flex-col transition-transform duration-200 ease-in-out md:static md:translate-x-0 md:min-h-screen shrink-0 shadow-3 md:shadow-none"
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

                <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Produk & Varian</span>
                    </span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori</span>
                    </span>
                </a>

                <a href="{{ route('admin.stock.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.stock.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
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

                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Pesanan Masuk</span>
                    </span>
                </a>

                <a href="{{ route('admin.partners.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.partners.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Grosir & Mitra</span>
                    </span>
                </a>

                <a href="{{ route('admin.shipping.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('admin.shipping.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <span>Zona Ongkir</span>
                    </span>
                </a>
            </nav>

            <!-- User / Logout Footer -->
            <div class="p-4 border-t border-primary-600 bg-primary-800/60">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="font-body text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Owner' }}</p>
                        <p class="font-body text-xs text-primary-200 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <!-- Theme Toggle Button -->
                        <x-theme-toggle class="text-primary-200 hover:text-gold-400 hover:bg-primary-700 p-1.5" />

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
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Topbar -->
            <header class="h-16 bg-white border-b border-neutral-200 px-4 flex items-center justify-between md:hidden shadow-1">
                <button @click="sidebarOpen = true" class="p-2 text-neutral-700 hover:bg-neutral-50 rounded-md">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-display font-bold text-primary-700">RHS HERITAGE</span>
                <div class="flex items-center gap-2">
                    <x-theme-toggle />
                </div>
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
</body>
</html>
