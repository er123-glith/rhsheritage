@props([
    'variant' => 'icon',
    'class' => '',
])

<button
    type="button"
    onclick="__toggleTheme()"
    title="Ubah Mode Gelap / Terang"
    aria-label="Ubah Mode Gelap / Terang"
    {{ $attributes->merge(['class' => 'relative inline-flex items-center justify-center p-2 rounded-full text-neutral-700 dark:text-gold-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition focus-visible:outline-primary-500 cursor-pointer ' . $class]) }}
>
    <!-- Sun Icon (Only shown in dark mode) -->
    <svg class="js-theme-icon-sun w-5 h-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>

    <!-- Moon Icon (Only shown in light mode) -->
    <svg class="js-theme-icon-moon w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>

    @if($variant === 'pill')
        <span class="ml-2 font-body text-xs font-semibold js-theme-text-light">Mode Gelap</span>
        <span class="ml-2 font-body text-xs font-semibold js-theme-text-dark">Mode Terang</span>
    @endif
</button>
