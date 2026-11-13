@props(['cartCount' => 0])

<nav class="md:hidden fixed bottom-3 inset-x-3 z-40 max-w-md mx-auto bg-white/95 dark:bg-[#14201B]/95 backdrop-blur-xl shadow-3 rounded-2xl border border-neutral-200/90 dark:border-neutral-700/80 p-1.5 flex items-center justify-around transition-all">
    <!-- Beranda -->
    <a
        href="{{ route('home') }}"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('home') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Beranda"
    >
        <i class="fa-solid fa-house text-base"></i>
        <span class="font-body text-[10px] font-semibold mt-1 leading-none">Beranda</span>
    </a>

    <!-- Katalog -->
    <a
        href="{{ route('catalog.index') }}"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('catalog.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Katalog"
    >
        <i class="fa-solid fa-layer-group text-base"></i>
        <span class="font-body text-[10px] font-semibold mt-1 leading-none">Katalog</span>
    </a>

    <!-- Keranjang Belanja -->
    <a
        href="{{ route('cart.index') }}"
        class="relative flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('cart.*') || request()->routeIs('checkout.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Keranjang"
    >
        <div class="relative">
            <i class="fa-solid fa-cart-shopping text-base"></i>
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-3 min-w-[18px] h-[18px] px-1 rounded-full bg-danger text-white font-body text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-neutral-900">
                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                </span>
            @endif
        </div>
        <span class="font-body text-[10px] font-semibold mt-1 leading-none">Keranjang</span>
    </a>

    {{-- Navigasi Dinamis Berdasarkan Status Login --}}
    @auth
        @if(auth()->user()->isAdmin())
            <a
                href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('admin.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
                aria-label="Panel Admin"
            >
                <i class="fa-solid fa-crown text-base text-gold-400"></i>
                <span class="font-body text-[10px] font-semibold mt-1 leading-none">Admin</span>
            </a>
        @else
            <a
                href="{{ route('customer.dashboard') }}"
                class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('customer.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
                aria-label="Panel Akun"
            >
                <i class="fa-solid fa-user text-base"></i>
                <span class="font-body text-[10px] font-semibold mt-1 leading-none">Akun</span>
            </a>
        @endif
    @else
        <a
            href="{{ route('login') }}"
            class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('login') || request()->routeIs('register') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
            aria-label="Masuk"
        >
            <i class="fa-solid fa-arrow-right-to-bracket text-base"></i>
            <span class="font-body text-[10px] font-semibold mt-1 leading-none">Masuk</span>
        </a>
    @endauth

    <!-- Menu Drawer Trigger -->
    <button
        type="button"
        @click="$dispatch('toggle-mobile-menu')"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400 transition-colors"
        aria-label="Buka Menu"
    >
        <i class="fa-solid fa-bars text-base"></i>
        <span class="font-body text-[10px] font-semibold mt-1 leading-none">Menu</span>
    </button>
</nav>
