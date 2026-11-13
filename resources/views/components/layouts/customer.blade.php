<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas-base">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Pelanggan' }} — RHS HERITAGE</title>

    <!-- Google Fonts — Apple / Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Theme Init & Global Toggle Script -->
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
        @php
            $sidebarActiveReturns = auth()->check()
                ? \App\Models\OrderReturn::where('user_id', auth()->id())
                    ->whereNotIn('status', [\App\Enums\ReturnStatus::Selesai->value, \App\Enums\ReturnStatus::Ditolak->value])
                    ->count()
                : 0;
        @endphp
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 w-72 h-screen bg-primary-700 text-white flex flex-col transition-transform duration-200 ease-in-out shrink-0 shadow-3 md:shadow-md"
        >
            <!-- Brand / Header -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-primary-600">
                <a href="{{ route('home') }}" class="block">
                    <span class="font-display text-xl font-bold tracking-wide text-gold-400">RHS HERITAGE</span>
                    <span class="block font-body text-xs text-primary-200 tracking-wider">PANEL CUSTOMER</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-primary-200 hover:text-white p-1" aria-label="Tutup Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-md font-body text-sm font-semibold transition {{ request()->routeIs('customer.dashboard') ? 'bg-primary-800 text-gold-400 border-l-4 border-gold-500' : 'text-primary-100 hover:bg-primary-600 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 font-body text-[10px] font-bold uppercase tracking-widest text-primary-300">Aktivitas Belanja</p>
                </div>

                <a href="{{ route('customer.orders') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('customer.orders*') ? 'bg-primary-800 text-gold-400 font-semibold' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Pesanan Saya</span>
                    </span>
                </a>

                <a href="{{ route('customer.returns.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('customer.returns*') ? 'bg-primary-800 text-gold-400 font-semibold' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        <span>Retur Saya</span>
                    </span>
                    @if($sidebarActiveReturns > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-neutral-900">
                            {{ $sidebarActiveReturns }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('cart.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('cart.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Keranjang Belanja</span>
                    </span>
                </a>

                <a href="{{ route('catalog.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium text-primary-200 hover:bg-primary-600 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Jelajahi Produk</span>
                    </span>
                    <span class="text-xs text-primary-300">↗</span>
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-4 font-body text-[10px] font-bold uppercase tracking-widest text-primary-300">Akun & Layanan</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-primary-800 text-gold-400' : 'text-primary-200 hover:bg-primary-600 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pengaturan Akun</span>
                    </span>
                </a>

                <a href="https://wa.me/{{ ltrim(config('whatsapp.destination_number', '6285728979999'), '+') }}?text={{ rawurlencode('Halo Admin RHS Heritage, saya ingin bertanya seputar produk dan pesanan saya.') }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium text-primary-200 hover:bg-primary-600 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Bantuan & WhatsApp</span>
                    </span>
                    <span class="text-xs text-primary-300">↗</span>
                </a>

                <a href="{{ route('home') }}" class="flex items-center justify-between px-4 py-2.5 rounded-md font-body text-sm font-medium text-primary-200 hover:bg-primary-600 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </span>
                </a>
            </nav>

            <!-- User / Logout Footer -->
            <div class="p-4 border-t border-primary-600 bg-primary-800/60">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1 mr-2">
                        <p class="font-body text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Pelanggan' }}</p>
                        <p class="font-body text-xs text-primary-200 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <!-- Theme Toggle Button -->
                        <x-theme-toggle class="text-primary-200 hover:text-gold-400 hover:bg-primary-700 p-1.5" />

                        <form method="POST" action="{{ route('logout') }}">
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
        <div class="flex-1 flex flex-col min-w-0 md:pl-72 min-h-screen">
            <!-- Mobile Topbar -->
            <header class="h-16 bg-white border-b border-neutral-200 px-4 flex items-center justify-between md:hidden shadow-1">
                <button @click="sidebarOpen = true" class="p-2 text-neutral-700 hover:bg-neutral-50 rounded-md" aria-label="Buka Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="text-center">
                    <span class="font-display font-bold text-primary-700 block text-base">RHS HERITAGE</span>
                    <span class="text-[10px] text-neutral-500 font-semibold tracking-wider block">PANEL CUSTOMER</span>
                </div>
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

