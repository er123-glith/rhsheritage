<x-layouts.public>
    <x-slot:title>{{ $product->name }} — RHS HERITAGE</x-slot:title>

    @php
        $variantsJson = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'label' => $v->variant_label,
                'price' => (float) $v->price,
                'stock' => (int) $v->stock_quantity,
                'is_active' => (bool) $v->is_active,
                'attribute_map' => $v->attribute_map ?? [],
            ];
        })->values();

        $firstVariant = $product->variants->firstWhere('stock_quantity', '>', 0) ?? $product->variants->first();
        $images = $product->images;
        $primaryImg = $images->first();
    @endphp

    <div class="space-y-12" x-data="{
        variants: {{ $variantsJson->toJson() }},
        selectedVariantId: {{ $firstVariant ? $firstVariant->id : 'null' }},
        quantity: 1,
        activeImage: '{{ $primaryImg ? $primaryImg->url : '' }}',

        get currentVariant() {
            return this.variants.find(v => v.id === this.selectedVariantId) || null;
        },

        get currentPrice() {
            if (!this.currentVariant) return {{ (float) $product->base_price }};
            return this.currentVariant.price;
        },

        get currentStock() {
            if (!this.currentVariant) return 0;
            return this.currentVariant.stock;
        },

        get isAvailable() {
            return this.currentVariant && this.currentVariant.is_active && this.currentVariant.stock > 0;
        },

        formatRupiah(num) {
            return 'Rp' + new Intl.NumberFormat('id-ID').format(num);
        }
    }">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-body text-neutral-500" data-aos="fade-down" data-aos-duration="600">
            <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-primary-700">Katalog</a>
            @if($product->category)
                <span>/</span>
                <a href="{{ route('catalog.category', $product->category->slug) }}" class="hover:text-primary-700">
                    {{ $product->category->name }}
                </a>
            @endif
            <span>/</span>
            <span class="text-primary-700 font-semibold truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <!-- Product Presentation Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white rounded-2xl p-6 sm:p-8 border border-neutral-200 shadow-1" data-aos="fade-up" data-aos-duration="700">
            <!-- Left: Gallery -->
            <div class="lg:col-span-6 space-y-4" data-aos="fade-right" data-aos-delay="100">
                <!-- Main Image Container (1:1 Ratio) -->
                <div class="relative aspect-square rounded-xl bg-canvas-sunken overflow-hidden border border-neutral-200/80">
                    <template x-if="activeImage">
                        <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-all duration-200">
                    </template>
                    <template x-if="!activeImage">
                        <div class="w-full h-full flex flex-col items-center justify-center p-8 text-neutral-400">
                            <svg class="w-16 h-16 text-primary-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span class="font-body text-xs text-neutral-500 font-medium">RHS HERITAGE</span>
                        </div>
                    </template>
                </div>

                <!-- Thumbnails -->
                @if($images->count() > 1)
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        @foreach($images as $img)
                            <button
                                type="button"
                                @click="activeImage = '{{ $img->url }}'"
                                class="w-18 h-18 rounded-lg overflow-hidden border-2 transition-all shrink-0"
                                :class="activeImage === '{{ $img->url }}' ? 'border-primary-700 ring-2 ring-primary-200' : 'border-neutral-200 hover:border-neutral-400'"
                            >
                                <img src="{{ $img->url }}" alt="{{ $img->alt_text ?? $product->name }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Details & Variant Selection Form -->
            <div class="lg:col-span-6 flex flex-col justify-between space-y-6" data-aos="fade-left" data-aos-delay="150">
                <div class="space-y-4">
                    @if($product->category)
                        <span class="inline-block font-body text-xs font-bold uppercase tracking-widest text-gold-700">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-800 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Realtime Price -->
                    <div class="flex items-baseline gap-3 py-2 border-y border-neutral-100">
                        <span class="font-body font-bold text-2xl sm:text-3xl text-primary-700" x-text="formatRupiah(currentPrice)">
                            Rp{{ number_format((float) $product->base_price, 0, ',', '.') }}
                        </span>
                        <template x-if="isAvailable && currentStock <= 5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-sm font-body text-xs font-semibold bg-warning-bg text-warning border border-amber-300">
                                Sisa <span x-text="currentStock" class="ml-1"></span> unit
                            </span>
                        </template>
                        <template x-if="!isAvailable">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-sm font-body text-xs font-bold bg-neutral-700 text-white">
                                Stok Habis
                            </span>
                        </template>
                    </div>

                    <!-- Description -->
                    <div class="prose prose-sm text-neutral-600 leading-relaxed font-body">
                        {!! nl2br(e($product->description)) !!}
                    </div>

                    <!-- Variant Selection -->
                    @if($product->variants->isNotEmpty())
                        <div class="space-y-3 pt-2">
                            <label class="font-body text-xs font-bold uppercase tracking-wider text-neutral-700 block">
                                Pilihan Varian / Ukuran:
                            </label>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                @foreach($product->variants as $variant)
                                    @php
                                        $varStock = $variant->stock_quantity;
                                        $varOut = $varStock <= 0;
                                    @endphp
                                    <button
                                        type="button"
                                        @click="selectedVariantId = {{ $variant->id }}"
                                        :class="{
                                            'border-primary-700 bg-primary-50/50 text-primary-800 ring-2 ring-primary-500/20 font-bold': selectedVariantId === {{ $variant->id }},
                                             'border-neutral-200 bg-white text-neutral-700 hover:border-neutral-300': selectedVariantId !== {{ $variant->id }} && !{{ $varOut ? 'true' : 'false' }},
                                            'border-neutral-200 bg-neutral-100 text-neutral-400 cursor-not-allowed opacity-60': {{ $varOut ? 'true' : 'false' }}
                                        }"
                                        class="px-3 py-2.5 rounded-md border text-xs font-body transition-all text-left flex flex-col justify-between min-h-[54px]"
                                    >
                                        <span class="font-semibold">{{ $variant->variant_label ?? $variant->sku }}</span>
                                        <span class="text-[11px] mt-1 {{ $varOut ? 'text-neutral-400' : 'text-primary-700 font-medium' }}">
                                            @if($varOut)
                                                Habis
                                            @else
                                                Rp{{ number_format((float) $variant->price, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Add to Cart & Actions -->
                <form action="{{ route('cart.store') }}" method="POST" class="pt-6 border-t border-neutral-200 space-y-4">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariantId">

                    <div class="flex items-center gap-4">
                        <label class="font-body text-xs font-semibold text-neutral-700 shrink-0">
                            Jumlah:
                        </label>
                        <x-quantity-stepper name="quantity" ::max="currentStock > 0 ? currentStock : 1" />
                        <span class="font-body text-xs text-neutral-500">
                            (Maksimal <span x-text="currentStock"></span>)
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <x-button
                            type="submit"
                            variant="primary"
                            size="lg"
                            class="flex-1"
                            x-bind:disabled="!isAvailable"
                        >
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span x-text="isAvailable ? 'Tambah ke Keranjang' : 'Stok Varian Habis'"></span>
                        </x-button>

                        <x-button
                            :href="route('partnership.index')"
                            variant="outlined"
                            size="lg"
                            class="text-neutral-700"
                        >
                            Pesan Jumlah Besar (Grosir)
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Related Products Section -->
        @if($relatedProducts->isNotEmpty())
            <section class="pt-6" data-aos="fade-up" data-aos-duration="650">
                <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-800 mb-6">
                    Karya Terkait dari Kategori Ini
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.public>
