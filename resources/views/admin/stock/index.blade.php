<x-layouts.admin>
    <x-slot:title>Kelola Stok Inventaris</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Kelola Stok</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Kelola Stok Inventaris</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Pantau ketersediaan seluruh varian produk, perbarui kuantitas stok secara cepat, dan tangani varian yang menipis atau habis.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gold-100/80 border border-gold-300 text-gold-900 text-xs font-semibold">
                    <svg class="w-4 h-4 text-gold-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batas Peringatan: ≤ {{ $threshold }} unit
                </span>
            </div>
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- All Variants -->
            <a href="{{ route('admin.stock.index') }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ !request('stock_status') ? 'bg-primary-50/80 border-primary-500 shadow-sm ring-1 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-primary-300 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Total Varian</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-primary-700 mt-1">{{ $totalVariants }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Semua produk bervarian</p>
            </a>

            <!-- In Stock -->
            <a href="{{ route('admin.stock.index', array_merge(request()->except('page'), ['stock_status' => 'in_stock'])) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('stock_status') === 'in_stock' ? 'bg-primary-50/80 border-primary-500 shadow-sm ring-1 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-primary-300 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Stok Aman</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-success mt-1">{{ $inStockCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Kuantitas > {{ $threshold }} unit</p>
            </a>

            <!-- Low Stock -->
            <a href="{{ route('admin.stock.index', array_merge(request()->except('page'), ['stock_status' => 'low_stock'])) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('stock_status') === 'low_stock' ? 'bg-warning-bg/90 border-warning shadow-sm ring-1 ring-warning/30' : 'bg-white border-neutral-200 hover:border-warning/50 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-warning transition">Stok Menipis</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-warning animate-pulse"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-warning mt-1">{{ $lowStockCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Sisa 1 s.d. {{ $threshold }} unit</p>
            </a>

            <!-- Out of Stock -->
            <a href="{{ route('admin.stock.index', array_merge(request()->except('page'), ['stock_status' => 'out_of_stock'])) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('stock_status') === 'out_of_stock' ? 'bg-danger-bg/90 border-danger shadow-sm ring-1 ring-danger/30' : 'bg-white border-neutral-200 hover:border-danger/50 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-danger transition">Stok Habis</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-danger"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-danger mt-1">{{ $outOfStockCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">0 unit (katalog nonaktif)</p>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('admin.stock.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama produk, SKU, atau varian..."
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="category_id" class="h-11 px-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 bg-white text-neutral-700">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="stock_status" class="h-11 px-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 bg-white text-neutral-700">
                <option value="">Semua Status Stok</option>
                <option value="in_stock" @selected(request('stock_status') === 'in_stock')>Stok Aman (> {{ $threshold }})</option>
                <option value="low_stock" @selected(request('stock_status') === 'low_stock')>Stok Menipis (1 - {{ $threshold }})</option>
                <option value="out_of_stock" @selected(request('stock_status') === 'out_of_stock')>Stok Habis (0)</option>
            </select>

            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="h-11 px-5">Filter</x-button>
                @if(request()->hasAny(['q', 'category_id', 'stock_status']))
                    <a href="{{ route('admin.stock.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Stock Table Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-canvas-sunken border-b border-neutral-200">
                        <tr>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Produk & Kategori</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">SKU / Varian</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Harga Jual</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status Stok</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 min-w-[280px]">Update Kuantitas</th>
                            <th class="px-5 py-3.5 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($variants as $variant)
                            <tr class="hover:bg-canvas-sunken/50 transition">
                                <!-- Product & Category with Thumbnail -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-neutral-100 border border-neutral-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if($variant->product->primaryImage)
                                                <img 
                                                    src="{{ $variant->product->primaryImage->url }}" 
                                                    alt="{{ $variant->product->name }}" 
                                                    class="w-full h-full object-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                >
                                                <div class="hidden w-full h-full items-center justify-center bg-neutral-100 text-neutral-400">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-neutral-100 text-neutral-400">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.products.edit', $variant->product_id) }}" class="font-body text-sm font-semibold text-neutral-900 hover:text-primary-700 transition truncate block" title="{{ $variant->product->name }}">
                                                {{ $variant->product->name }}
                                            </a>
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[11px] font-medium bg-neutral-100 text-neutral-600">
                                                {{ $variant->product->category->name ?? 'Tanpa Kategori' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- SKU & Variant Label -->
                                <td class="px-5 py-4 font-body text-sm">
                                    <span class="font-semibold text-neutral-800 block">{{ $variant->variant_label }}</span>
                                    <span class="text-xs text-neutral-400 font-mono">{{ $variant->sku ?: '-' }}</span>
                                </td>

                                <!-- Price -->
                                <td class="px-5 py-4 font-body text-sm text-neutral-800 font-semibold">
                                    Rp{{ number_format($variant->price, 0, ',', '.') }}
                                </td>

                                <!-- Stock Status Badge -->
                                <td class="px-5 py-4">
                                    @if($variant->stock_quantity <= 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-danger-bg text-danger border border-danger/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-danger"></span>
                                            Habis (0)
                                        </span>
                                    @elseif($variant->stock_quantity <= $threshold)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-warning-bg text-warning border border-warning/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-warning"></span>
                                            Menipis ({{ $variant->stock_quantity }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-primary-50 text-success border border-primary-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                                            Tersedia ({{ $variant->stock_quantity }})
                                        </span>
                                    @endif
                                </td>

                                <!-- Update Stock Quantity Inline Form -->
                                <td class="px-5 py-4">
                                    <form method="POST" action="{{ route('admin.stock.update', $variant) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative w-24">
                                            <input
                                                type="number"
                                                name="stock_quantity"
                                                min="0"
                                                max="99999"
                                                value="{{ $variant->stock_quantity }}"
                                                required
                                                class="w-full h-9 px-3 text-center font-body text-sm font-semibold rounded-lg border border-neutral-300 focus:outline-none focus:border-primary-700 focus:ring-1 focus:ring-primary-700 {{ $variant->stock_quantity <= 0 ? 'bg-danger-bg/50 border-danger/40 text-danger' : ($variant->stock_quantity <= $threshold ? 'bg-warning-bg/40 border-warning/40 text-neutral-800' : 'bg-white') }}"
                                            />
                                        </div>

                                        <button
                                            type="submit"
                                            title="Simpan Kuantitas Stok"
                                            class="h-9 px-3.5 bg-primary-700 hover:bg-primary-800 text-white rounded-lg font-body text-xs font-semibold transition flex items-center gap-1.5 shadow-sm active:scale-95"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Simpan</span>
                                        </button>
                                    </form>

                                    <!-- Quick delta adjustments -->
                                    <div class="flex items-center gap-1.5 mt-2 text-xs text-neutral-500 font-mono">
                                        <span class="text-[11px] text-neutral-400">Cepat:</span>
                                        <form method="POST" action="{{ route('admin.stock.quick-adjust', $variant) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delta" value="1">
                                            <button type="submit" class="px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-primary-100 text-neutral-700 hover:text-primary-700 border border-neutral-200 transition text-xs font-semibold" title="Tambah 1">+1</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.stock.quick-adjust', $variant) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delta" value="5">
                                            <button type="submit" class="px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-primary-100 text-neutral-700 hover:text-primary-700 border border-neutral-200 transition text-xs font-semibold" title="Tambah 5">+5</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.stock.quick-adjust', $variant) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delta" value="10">
                                            <button type="submit" class="px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-primary-100 text-neutral-700 hover:text-primary-700 border border-neutral-200 transition text-xs font-semibold" title="Tambah 10">+10</button>
                                        </form>
                                        @if($variant->stock_quantity > 0)
                                            <form method="POST" action="{{ route('admin.stock.quick-adjust', $variant) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="delta" value="-1">
                                                <button type="submit" class="px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-danger-bg text-neutral-700 hover:text-danger border border-neutral-200 transition text-xs font-semibold" title="Kurang 1">-1</button>
                                            </form>
                                        @endif
                                        @if($variant->stock_quantity >= 5)
                                            <form method="POST" action="{{ route('admin.stock.quick-adjust', $variant) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="delta" value="-5">
                                                <button type="submit" class="px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-danger-bg text-neutral-700 hover:text-danger border border-neutral-200 transition text-xs font-semibold" title="Kurang 5">-5</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>

                                <!-- Action (Link to edit Product) -->
                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="{{ route('admin.products.edit', $variant->product_id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary-200 text-xs font-semibold text-primary-700 hover:bg-primary-50 transition"
                                        title="Buka halaman edit produk & varian"
                                    >
                                        <span>Kelola Produk</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mx-auto mb-4 text-neutral-400">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <p class="font-display font-bold text-neutral-800 text-base">Tidak ada varian yang ditemukan</p>
                                        <p class="font-body text-xs text-neutral-500 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter untuk melihat seluruh data kembali.</p>
                                        <a href="{{ route('admin.stock.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 text-white font-body text-xs font-semibold hover:bg-primary-800 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            <span>Tampilkan Semua Varian</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($variants->hasPages())
                <div class="px-5 py-4 border-t border-neutral-200 bg-canvas-sunken/40">
                    {{ $variants->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
