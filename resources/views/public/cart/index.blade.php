<x-layouts.public>
    <x-slot:title>Keranjang Belanja — RHS HERITAGE</x-slot:title>

    <div class="space-y-8">
        <!-- Breadcrumbs & Heading -->
        <div data-aos="fade-down" data-aos-duration="600">
            <nav class="flex items-center gap-2 text-xs font-body text-neutral-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                <span>/</span>
                <span class="text-primary-700 font-semibold">Keranjang Belanja</span>
            </nav>

            <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800">
                Keranjang Belanja Anda
            </h1>
        </div>

        @if($summary->items->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Cart Items List (8 cols) -->
                <div class="lg:col-span-8 bg-white rounded-2xl border border-neutral-200 shadow-1 overflow-hidden" data-aos="fade-right" data-aos-duration="650">
                    <div class="p-4 sm:p-6 border-b border-neutral-100 flex items-center justify-between">
                        <span class="font-body text-xs font-bold uppercase tracking-wider text-neutral-500">
                            Daftar Produk ({{ $summary->items->count() }} item)
                        </span>
                        <span class="font-body text-xs text-neutral-500">
                            Total Berat: {{ number_format($summary->totalWeightGram() / 1000, 2, ',', '.') }} kg
                        </span>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        @foreach($summary->items as $item)
                            @php
                                $product = $item->variant->product;
                                $img = $product->primaryImage ?? $product->images->first();
                            @endphp
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <!-- Product Info -->
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-lg bg-canvas-sunken overflow-hidden shrink-0 border border-neutral-200">
                                        @if($img)
                                             <img src="{{ $img->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-neutral-400">
                                                <svg class="w-8 h-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        @if($product->category)
                                            <span class="font-body text-[11px] font-semibold uppercase tracking-wider text-neutral-400 block">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif

                                        <h3 class="font-body font-semibold text-sm sm:text-base text-neutral-800 hover:text-primary-700">
                                            <a href="{{ route('product.show', $product->slug) }}">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        <p class="font-body text-xs text-neutral-600 mt-0.5">
                                            Varian: <span class="font-semibold text-primary-700">{{ $item->variant->variant_label ?? $item->variant->sku }}</span>
                                        </p>

                                        <p class="font-body text-xs text-neutral-500 mt-1">
                                            <x-price :amount="$item->unitPrice()" size="sm" /> / unit
                                        </p>
                                    </div>
                                </div>

                                <!-- Quantity Controller & Subtotal -->
                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-neutral-100">
                                    <!-- Update Quantity Form -->
                                    <form action="{{ route('cart.update', $item->variantId) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="inline-flex items-center h-9 border border-neutral-200 rounded-md bg-canvas-raised overflow-hidden">
                                            <button
                                                type="submit"
                                                name="quantity"
                                                value="{{ max(1, $item->quantity - 1) }}"
                                                class="w-8 h-full flex items-center justify-center text-neutral-600 hover:bg-neutral-100 disabled:opacity-30"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}
                                            >
                                                &minus;
                                            </button>
                                            <span class="w-10 text-center font-body text-xs font-semibold text-neutral-800">
                                                {{ $item->quantity }}
                                            </span>
                                            <button
                                                type="submit"
                                                name="quantity"
                                                value="{{ $item->quantity + 1 }}"
                                                class="w-8 h-full flex items-center justify-center text-neutral-600 hover:bg-neutral-100 disabled:opacity-30"
                                                {{ $item->quantity >= $item->variant->stock_quantity ? 'disabled' : '' }}
                                            >
                                                &plus;
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Subtotal -->
                                    <div class="text-right min-w-[100px]">
                                        <span class="font-body text-[11px] text-neutral-400 block sm:hidden">Subtotal:</span>
                                        <x-price :amount="$item->subtotal()" size="md" class="text-neutral-800 font-bold" />
                                    </div>

                                    <!-- Delete Item Form -->
                                    <form action="{{ route('cart.destroy', $item->variantId) }}" method="POST" data-confirm="Hapus produk ini dari keranjang?">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-2 text-neutral-400 hover:text-danger rounded-md transition-colors"
                                            title="Hapus dari keranjang"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Summary & Checkout Sidebar (4 cols) -->
                <div class="lg:col-span-4 bg-white rounded-2xl border border-neutral-200 p-6 shadow-1 space-y-6 sticky top-28" data-aos="fade-left" data-aos-delay="150" data-aos-duration="650">
                    <h2 class="font-display text-lg font-bold text-neutral-800 border-b border-neutral-100 pb-3">
                        Ringkasan Pesanan
                    </h2>

                    <div class="space-y-3 font-body text-xs sm:text-sm text-neutral-600">
                        <div class="flex items-center justify-between">
                            <span>Total Unit:</span>
                            <span class="font-semibold text-neutral-800">{{ $summary->itemCount() }} pcs</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Total Berat:</span>
                            <span class="font-semibold text-neutral-800">{{ number_format($summary->totalWeightGram() / 1000, 2, ',', '.') }} kg</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-neutral-100 text-sm">
                            <span class="font-medium text-neutral-700">Subtotal Belanja:</span>
                            <x-price :amount="$summary->subtotal" size="lg" class="text-primary-700 font-bold" />
                        </div>
                    </div>

                    <p class="font-body text-[11px] text-neutral-500 leading-relaxed bg-canvas-sunken p-3 rounded-lg">
                        * Ongkos kirim dihitung pada tahap checkout sesuai alamat dan zona pengiriman pilihan Anda.
                    </p>

                    <div class="space-y-3 pt-2">
                        <x-button :href="route('checkout.index')" variant="primary" size="lg" class="w-full shadow-md">
                            Lanjut ke Form Pemesanan &rarr;
                        </x-button>

                        <x-button :href="route('catalog.index')" variant="outlined" size="md" class="w-full">
                            Tambah Produk Lain
                        </x-button>
                    </div>
                </div>
            </div>
        @else
            <div data-aos="fade-up">
                <x-empty-state
                    title="Keranjang Belanja Masih Kosong"
                    message="Anda belum menambahkan produk apa pun ke keranjang. Jelajahi katalog pilihan kriya songkok, sarung ATBM, dan jilbab sekolah kami."
                    action-label="Mulai Belanja Sekarang"
                    :action-url="route('catalog.index')"
                />
            </div>
        @endif
    </div>
</x-layouts.public>
