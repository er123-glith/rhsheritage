<x-layouts.admin>
    <x-slot:title>Produk</x-slot:title>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-display text-2xl font-bold text-primary-700">Produk</h1>
            <p class="font-body text-sm text-neutral-500 mt-1">Kelola katalog, foto, dan varian produk RHS HERITAGE.</p>
        </div>
        <x-button variant="primary" :href="route('admin.products.create')">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Produk</span>
        </x-button>
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama produk..."
            class="flex-1 h-11 px-4 rounded-md font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
        />
        <select name="category_id" class="h-11 px-4 rounded-md font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-canvas-sunken border-b border-neutral-200">
                <tr>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Produk</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Kategori</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Harga Dasar</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Varian</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Stok</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Status</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($products as $product)
                    <tr class="hover:bg-canvas-sunken/60 transition">
                        <td class="px-5 py-4 font-body text-sm font-semibold text-neutral-700">{{ $product->name }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $product->category->name }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">Rp{{ number_format($product->base_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $product->variants_count }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $product->variants_sum_stock_quantity ?? 0 }}</td>
                        <td class="px-5 py-4">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-sm bg-primary-50 text-primary-700 font-body text-xs font-semibold">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-sm bg-neutral-100 text-neutral-500 font-body text-xs font-semibold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-button variant="outlined" size="sm" :href="route('admin.products.edit', $product)">Kelola</x-button>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="Hapus produk {{ $product->name }} beserta seluruh varian dan foto?">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm">Hapus</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center font-body text-sm text-neutral-500">Belum ada produk. Tambahkan produk pertama Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</x-layouts.admin>
