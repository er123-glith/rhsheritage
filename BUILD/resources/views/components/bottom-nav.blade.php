@props(['cartCount' => 0])

<nav class="md:hidden fixed bottom-3 inset-x-3 z-40 max-w-md mx-auto bg-white/95 dark:bg-[#14201B]/95 backdrop-blur-xl shadow-3 rounded-2xl border border-neutral-200/90 dark:border-neutral-700/80 p-1.5 flex items-center justify-around transition-all">
    <!-- Beranda -->
    <a
        href="{{ route('home') }}"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('home') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Beranda"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="font-body text-[10px] font-semibold mt-0.5 leading-none">Beranda</span>
    </a>

    <!-- Katalog -->
    <a
        href="{{ route('catalog.index') }}"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('catalog.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Katalog"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <span class="font-body text-[10px] font-semibold mt-0.5 leading-none">Katalog</span>
    </a>

    <!-- Keranjang Belanja -->
    <a
        href="{{ route('cart.index') }}"
        class="relative flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('cart.*') || request()->routeIs('checkout.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Keranjang"
    >
        <div class="relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            @if($cartCount > 0)
                <span class="absolute -top-1.5 -right-2.5 min-w-[18px] h-[18px] px-1 rounded-full bg-danger text-white font-body text-[10px] font-bold flex items-center justify-center ring-2 ring-white dark:ring-neutral-900">
                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                </span>
            @endif
        </div>
        <span class="font-body text-[10px] font-semibold mt-0.5 leading-none">Keranjang</span>
    </a>

    <!-- Grosir & Mitra -->
    <a
        href="{{ route('partnership.index') }}"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl transition-all {{ request()->routeIs('partnership.*') ? 'bg-primary-700 dark:bg-primary-600 text-white font-bold shadow-xs' : 'text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400' }}"
        aria-label="Grosir & Mitra"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="font-body text-[10px] font-semibold mt-0.5 leading-none">Grosir</span>
    </a>

    <!-- Menu Drawer Trigger -->
    <button
        type="button"
        @click="$dispatch('toggle-mobile-menu')"
        class="flex flex-col items-center justify-center flex-1 h-12 rounded-xl text-neutral-600 dark:text-neutral-400 hover:text-primary-700 dark:hover:text-gold-400 transition-colors"
        aria-label="Buka Menu"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="font-body text-[10px] font-semibold mt-0.5 leading-none">Menu</span>
    </button>
</nav>
