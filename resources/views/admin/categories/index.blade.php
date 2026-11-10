<x-layouts.admin>
    <x-slot:title>Kategori Produk</x-slot:title>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-display text-2xl font-bold text-primary-700">Kategori Produk</h1>
            <p class="font-body text-sm text-neutral-500 mt-1">Kelola kategori untuk mengelompokkan produk pada katalog.</p>
        </div>
        <x-button variant="primary" :href="route('admin.categories.create')">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kategori</span>
        </x-button>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-canvas-sunken border-b border-neutral-200">
                <tr>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Nama</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Slug</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Urutan</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Produk</th>
                    <th class="px-5 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-canvas-sunken/60 transition">
                        <td class="px-5 py-4 font-body text-sm font-semibold text-neutral-700">{{ $category->name }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $category->slug }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $category->sort_order }}</td>
                        <td class="px-5 py-4 font-body text-sm text-neutral-500">{{ $category->products_count }}</td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <x-button variant="outlined" size="sm" :href="route('admin.categories.edit', $category)">Ubah</x-button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Hapus kategori {{ $category->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger" size="sm">Hapus</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center font-body text-sm text-neutral-500">Belum ada kategori. Tambahkan kategori pertama Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
