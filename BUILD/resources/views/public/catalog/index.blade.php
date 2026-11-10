<x-layouts.public>
    <x-slot:title>
        {{ isset($category) ? $category->name . ' — Katalog RHS HERITAGE' : 'Katalog Kriya Pilihan — RHS HERITAGE' }}
    </x-slot:title>

    <div class="space-y-8">
        <!-- Header & Breadcrumbs -->
        <div data-aos="fade-down" data-aos-duration="600">
            <nav class="flex items-center gap-2 text-xs font-body text-neutral-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('catalog.index') }}" class="hover:text-primary-700 {{ !isset($category) ? 'text-primary-700 font-semibold' : '' }}">Katalog</a>
                @if(isset($category))
                    <span>/</span>
                    <span class="text-primary-700 font-semibold">{{ $category->name }}</span>
                @endif
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2">
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800">
                    {{ isset($category) ? 'Koleksi ' . $category->name : 'Seluruh Katalog Kriya' }}
                </h1>
                <p class="font-body text-xs text-neutral-500">
                    Menampilkan {{ $products->total() }} produk pilihan
                </p>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-neutral-200 shadow-1 space-y-4" data-aos="fade-up" data-aos-duration="650">
            <!-- Category Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <a
                    href="{{ route('catalog.index', array_merge(request()->except('page'), ['category' => null])) }}"
                    class="px-4 py-2 rounded-full font-body text-xs font-semibold shrink-0 transition-colors {{ !isset($category) ? 'bg-primary-700 text-white shadow-sm' : 'bg-canvas-sunken text-neutral-700 hover:bg-neutral-200' }}"
                >
                    Semua Kategori
                </a>

                @foreach($categories as $cat)
                    @php
                        $isActive = isset($category) && $category->id === $cat->id;
                    @endphp
                    <a
                        href="{{ route('catalog.category', $cat->slug) }}"
                        class="px-4 py-2 rounded-full font-body text-xs font-semibold shrink-0 transition-colors {{ $isActive ? 'bg-primary-700 text-white shadow-sm' : 'bg-canvas-sunken text-neutral-700 hover:bg-neutral-200' }}"
                    >
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <!-- Search Form and In-Stock Toggle -->
            <form action="{{ isset($category) ? route('catalog.category', $category->slug) : route('catalog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-3 border-t border-neutral-100">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama produk, motif, atau bahan..."
                        class="w-full h-10 pl-10 pr-4 rounded-md bg-canvas-sunken text-sm text-neutral-800 placeholder-neutral-500 border border-neutral-200 focus:border-primary-500 focus:bg-white focus:outline-none"
                    >
                </div>

                <div class="flex items-center gap-4 shrink-0">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="in_stock"
                            value="1"
                            {{ request()->boolean('in_stock') ? 'checked' : '' }}
                            onchange="this.form.submit()"
                            class="w-4 h-4 rounded text-primary-700 border-neutral-300 focus:ring-primary-500"
                        >
                        <span class="font-body text-xs font-medium text-neutral-700">Hanya Stok Tersedia</span>
                    </label>

                    <x-button type="submit" variant="secondary" size="sm">
                        Terapkan
                    </x-button>

                    @if(request('q') || request('in_stock'))
                        <a href="{{ isset($category) ? route('catalog.category', $category->slug) : route('catalog.index') }}" class="text-xs text-neutral-500 hover:text-danger">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6" data-aos="fade-up" data-aos-duration="650">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-6" data-aos="fade-up">
                {{ $products->links() }}
            </div>
        @else
            <div data-aos="fade-up">
                <x-empty-state
                    title="Produk Tidak Ditemukan"
                    message="Maaf, tidak ada produk yang cocok dengan kata kunci atau filter yang Anda pilih. Coba kurangi filter atau telusuri kategori lainnya."
                    action-label="Reset Semua Filter"
                    :action-url="route('catalog.index')"
                />
            </div>
        @endif
    </div>
</x-layouts.public>
