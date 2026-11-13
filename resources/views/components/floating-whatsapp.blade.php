@php
    $whatsappNumber = config('whatsapp.destination_number', '6285728979999');
    $initialMessage = rawurlencode('Halo RHS Heritage, saya ingin bertanya mengenai produk yang tersedia.');
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$initialMessage}";
@endphp

<!-- Floating WhatsApp Button (RHS Heritage Official Customer Care) -->
<div class="fixed bottom-20 md:bottom-6 right-4 sm:right-6 z-40 group print:hidden">
    <!-- Tooltip (Desktop) -->
    <div class="hidden sm:block absolute right-full mr-3 top-1/2 -translate-y-1/2 whitespace-nowrap px-3.5 py-1.5 rounded-xl bg-neutral-900/95 dark:bg-[#15221d] text-white dark:text-neutral-100 font-body text-xs font-semibold shadow-xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-200 border border-neutral-700/60">
        <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Hubungi Kami via WhatsApp</span>
        </span>
        <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-neutral-900/95 dark:bg-[#15221d] rotate-45 border-r border-t border-neutral-700/60"></div>
    </div>

    <!-- WhatsApp Floating Action Button -->
    <a
        href="{{ $whatsappUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#25D366] hover:bg-[#1EBE5D] text-white flex items-center justify-center shadow-2 hover:shadow-3 transform hover:scale-105 active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-400/50"
        aria-label="Hubungi Kami via WhatsApp"
        title="Hubungi Kami via WhatsApp"
    >
        <!-- Subtle ping animation ring -->
        <span class="absolute inset-0 rounded-full bg-[#25D366] opacity-30 animate-ping pointer-events-none"></span>

        <!-- Font Awesome WhatsApp Icon -->
        <i class="fa-brands fa-whatsapp text-2xl sm:text-3xl relative z-10"></i>
    </a>
</div>
