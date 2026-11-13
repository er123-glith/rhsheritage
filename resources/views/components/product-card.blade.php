@props(['product', 'variant' => null])

@php
$image = $product->relationLoaded('images') ? $product->images->first() : ($product->relationLoaded('primaryImage') ? $product->primaryImage : null);
$category = $product->relationLoaded('category') ? $product->category : null;
$activeVariants = $product->relationLoaded('variants') ? $product->variants->where('is_active', true) : collect();
$minPrice = $variant ? $variant->price : ($activeVariants->min('price') ?? $product->base_price);
$totalStock = $activeVariants->sum('stock_quantity');
$isOutOfStock = $totalStock <= 0;
$isLowStock = $totalStock > 0 && $totalStock <= 5;
$soldUnits = $product->total_sold ?? null;
@endphp

<article {{ $attributes->merge(['class' => 'group relative flex flex-col bg-white dark:bg-[#15221d] rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shadow-1 hover:shadow-2 hover:-translate-y-1 transition-all duration-200']) }}>
    <!-- Image Section (1:1 Aspect Ratio) -->
    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-square bg-canvas-sunken dark:bg-neutral-800 overflow-hidden">
        @if($image)
            <img
                src="{{ $image->url }}"
                alt="{{ $image->alt_text ?? $product->name }}"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300 {{ $isOutOfStock ? 'opacity-50 grayscale' : '' }}"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-neutral-400 bg-neutral-100 dark:bg-neutral-800 {{ $isOutOfStock ? 'opacity-50' : '' }}">
                <i class="fa-solid fa-box-open text-3xl text-neutral-300 dark:text-neutral-600 mb-2"></i>
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

        <!-- Official Store Chip -->
        <div class="absolute bottom-2 left-2 z-10 pointer-events-none">
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-primary-900/85 backdrop-blur-xs text-gold-300 font-body text-[9px] font-bold tracking-wider uppercase">
                <i class="fa-solid fa-crown text-[8px] text-gold-400"></i>
                <span>Official</span>
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
            <h3 class="font-body font-semibold text-xs sm:text-sm text-neutral-800 dark:text-neutral-100 line-clamp-2 leading-snug group-hover:text-primary-700 dark:group-hover:text-gold-400 transition-colors">
                <a href="{{ route('product.show', $product->slug) }}" title="{{ $product->name }}">
                    {{ $product->name }}
                </a>
            </h3>

            <!-- Price -->
            <div class="mt-2">
                <p class="font-body font-bold text-sm sm:text-base text-primary-700 dark:text-gold-400">
                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Stock / Verified Metric Footer -->
        <div class="mt-2.5 pt-2 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between text-[11px] text-neutral-500 dark:text-neutral-400">
            @if($soldUnits !== null)
                <span class="text-amber-600 dark:text-amber-400 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                    <span>Terjual {{ $soldUnits }} unit</span>
                </span>
            @else
                <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    <span>Kriya Tradisi</span>
                </span>
            @endif

            @if($totalStock > 0)
                <span class="text-[10px] text-neutral-400">Stok: {{ $totalStock }}</span>
            @endif
        </div>
    </div>
</article>
