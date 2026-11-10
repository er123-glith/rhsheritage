@props(['product', 'variant' => null])

@php
$image = $product->relationLoaded('images') ? $product->images->first() : ($product->relationLoaded('primaryImage') ? $product->primaryImage : null);
$category = $product->relationLoaded('category') ? $product->category : null;
$activeVariants = $product->relationLoaded('variants') ? $product->variants->where('is_active', true) : collect();
$minPrice = $variant ? $variant->price : ($activeVariants->min('price') ?? $product->base_price);
$totalStock = $activeVariants->sum('stock_quantity');
$isOutOfStock = $totalStock <= 0;
$isLowStock = $totalStock > 0 && $totalStock <= 5;
@endphp

<article {{ $attributes->merge(['class' => 'group relative flex flex-col bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-1 hover:shadow-2 hover:-translate-y-1 transition-all duration-200']) }}>
    <!-- Image Section (1:1 Aspect Ratio) -->
    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-square bg-canvas-sunken overflow-hidden">
        @if($image)
            <img
                src="{{ $image->url }}"
                alt="{{ $image->alt_text ?? $product->name }}"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 {{ $isOutOfStock ? 'opacity-50 grayscale' : '' }}"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-neutral-400 bg-neutral-100 {{ $isOutOfStock ? 'opacity-50' : '' }}">
                <svg class="w-10 h-10 text-neutral-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="font-body text-[11px] font-semibold text-neutral-400">RHS HERITAGE</span>
            </div>
        @endif

        <!-- Badges on Image -->
        <div class="absolute top-2 left-2 flex flex-col gap-1 z-10 pointer-events-none">
            @if($isOutOfStock)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-800 text-white shadow-xs">
                    Stok Habis
                </span>
            @elseif($isLowStock)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-neutral-900 shadow-xs">
                    Sisa {{ $totalStock }}
                </span>
            @endif
        </div>

        <!-- Official Store Chip (Tokopedia Style) -->
        <div class="absolute bottom-2 left-2 z-10 pointer-events-none">
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-primary-900/85 backdrop-blur-xs text-gold-300 font-body text-[9px] font-bold tracking-wider uppercase">
                <span>👑</span> Official
            </span>
        </div>
    </a>

    <!-- Content Section -->
    <div class="p-3.5 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category -->
            @if($category)
                <p class="font-body text-[10px] font-semibold uppercase tracking-wider text-neutral-400 mb-0.5">
                    {{ $category->name }}
                </p>
            @endif

            <!-- Product Title -->
            <h3 class="font-body font-semibold text-xs sm:text-sm text-neutral-800 line-clamp-2 leading-snug group-hover:text-primary-700 transition-colors">
                <a href="{{ route('product.show', $product->slug) }}" title="{{ $product->name }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Price -->
            <div class="mt-2">
                <p class="font-body font-bold text-sm sm:text-base text-primary-700">
                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Rating & Sold Count Footer -->
        <div class="mt-2.5 pt-2 border-t border-neutral-100 flex items-center gap-1 text-[11px] text-neutral-500">
            <span class="text-amber-400 font-bold">★</span>
            <span class="font-semibold text-neutral-700">5.0</span>
            <span class="text-neutral-300">•</span>
            <span>Terjual 50+</span>
        </div>
    </div>
</article>
