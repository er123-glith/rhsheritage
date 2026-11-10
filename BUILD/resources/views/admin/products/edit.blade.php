<x-layouts.admin>
    <x-slot:title>Kelola Produk</x-slot:title>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-bold text-primary-700">{{ $product->name }}</h1>
            <p class="font-body text-sm text-neutral-500 mt-1">Kelola informasi dasar, foto, dan varian produk.</p>
        </div>
        <x-button variant="outlined" :href="route('admin.products.index')">&larr; Kembali</x-button>
    </div>

    <div class="space-y-6">
        <!-- Informasi Dasar -->
        <section class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-primary-700 mb-5">Informasi Dasar</h2>

            <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-form.select
                    name="category_id"
                    label="Kategori"
                    required
                    :options="$categories->pluck('name', 'id')"
                    :value="old('category_id', $product->category_id)"
                />

                <x-form.input name="name" label="Nama Produk" required :value="old('name', $product->name)" />

                <x-form.textarea name="description" label="Deskripsi" :value="old('description', $product->description)" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form.input name="base_price" type="number" label="Harga Dasar (Rp)" required min="0" step="0.01" :value="old('base_price', $product->base_price)" />
                    <x-form.input name="weight_gram" type="number" label="Berat (gram)" required min="1" :value="old('weight_gram', $product->weight_gram)" />
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="w-4 h-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer" />
                    <span class="font-body text-sm text-neutral-700">Aktifkan produk di katalog publik</span>
                </label>

                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
            </form>
        </section>

        <!-- Foto Produk -->
        <section class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-primary-700 mb-5">Foto Produk</h2>

            @if($product->images->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    @foreach($product->images as $image)
                        <div class="relative group rounded-lg overflow-hidden border border-neutral-200 aspect-square">
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="w-full h-full object-cover" />
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" data-confirm="Hapus foto ini?" class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-neutral-900/70 text-white flex items-center justify-center hover:bg-danger transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="font-body text-sm text-neutral-500 mb-6">Belum ada foto. Unggah minimal satu foto agar produk dapat ditampilkan di katalog.</p>
            @endif

            <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="space-y-4 border-t border-neutral-100 pt-5">
                @csrf
                <div class="space-y-2">
                    <label for="images" class="block font-body text-xs font-semibold uppercase tracking-wider text-neutral-700">Unggah Foto (maks. 5, JPG/PNG/WebP, maks. 4MB per foto)</label>
                    <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full font-body text-sm text-neutral-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-700 file:font-semibold file:text-sm hover:file:bg-primary-100" />
                    @error('images')
                        <p class="font-body text-xs text-danger">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="font-body text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <x-form.input name="alt_text" label="Teks Alternatif (opsional)" placeholder="Deskripsi singkat foto untuk aksesibilitas" />
                <x-button type="submit" variant="primary">Unggah Foto</x-button>
            </form>
        </section>

        <!-- Varian Produk -->
        <section class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-primary-700 mb-1">Varian Produk</h2>
            <p class="font-body text-sm text-neutral-500 mb-5">Setiap produk wajib memiliki minimal satu varian. Atribut (contoh: Ukuran, Motif) dibuat bebas sesuai kebutuhan.</p>

            <div class="overflow-x-auto mb-6">
                <table class="w-full text-left min-w-[640px]">
                    <thead class="bg-canvas-sunken border-b border-neutral-200">
                        <tr>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Label Varian</th>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">SKU</th>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Harga</th>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Stok</th>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500">Aktif</th>
                            <th class="px-4 py-3 font-body text-xs font-semibold uppercase tracking-wider text-neutral-500 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($product->variants as $variant)
                            <tr x-data="{ editing: false }">
                                <td class="px-4 py-3 font-body text-sm font-semibold text-neutral-700" colspan="1" x-show="!editing">{{ $variant->variant_label }}</td>
                                <td colspan="5" x-show="editing" x-cloak class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.variants.update', [$product, $variant]) }}" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <div class="w-32">
                                            <label class="block font-body text-[10px] font-semibold uppercase text-neutral-500 mb-1">SKU</label>
                                            <input type="text" name="sku" value="{{ $variant->sku }}" class="w-full h-9 px-3 rounded-md text-sm border border-neutral-200" />
                                        </div>
                                        <div class="w-28">
                                            <label class="block font-body text-[10px] font-semibold uppercase text-neutral-500 mb-1">Harga</label>
                                            <input type="number" name="price" step="0.01" min="0" value="{{ $variant->price }}" required class="w-full h-9 px-3 rounded-md text-sm border border-neutral-200" />
                                        </div>
                                        <div class="w-24">
                                            <label class="block font-body text-[10px] font-semibold uppercase text-neutral-500 mb-1">Stok</label>
                                            <input type="number" name="stock_quantity" min="0" value="{{ $variant->stock_quantity }}" required class="w-full h-9 px-3 rounded-md text-sm border border-neutral-200" />
                                        </div>
                                        <label class="flex items-center gap-1.5 h-9">
                                            <input type="checkbox" name="is_active" value="1" @checked($variant->is_active) class="w-4 h-4 rounded border-neutral-300 text-primary-700" />
                                            <span class="font-body text-xs text-neutral-600">Aktif</span>
                                        </label>
                                        <x-button type="submit" variant="primary" size="sm">Simpan</x-button>
                                        <x-button type="button" variant="outlined" size="sm" @click="editing = false">Batal</x-button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 font-body text-sm text-neutral-500" x-show="!editing">{{ $variant->sku ?: '—' }}</td>
                                <td class="px-4 py-3 font-body text-sm text-neutral-500" x-show="!editing">Rp{{ number_format($variant->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-body text-sm text-neutral-500" x-show="!editing">{{ $variant->stock_quantity }}</td>
                                <td class="px-4 py-3" x-show="!editing">
                                    @if($variant->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm bg-primary-50 text-primary-700 font-body text-xs font-semibold">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-sm bg-neutral-100 text-neutral-500 font-body text-xs font-semibold">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" x-show="!editing">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button type="button" variant="outlined" size="sm" @click="editing = true">Ubah</x-button>
                                        <form method="POST" action="{{ route('admin.variants.destroy', [$product, $variant]) }}" data-confirm="Hapus varian {{ $variant->variant_label }}?">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger" size="sm">Hapus</x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                x-data="{
                    rows: [{ name: '', value: '' }],
                    addRow() { this.rows.push({ name: '', value: '' }) },
                    removeRow(i) { if (this.rows.length > 1) this.rows.splice(i, 1) },
                }"
                class="border-t border-neutral-100 pt-5"
            >
                <h3 class="font-body text-sm font-semibold text-neutral-700 mb-4">Tambah Varian Baru</h3>

                <form method="POST" action="{{ route('admin.variants.store', $product) }}" class="space-y-4">
                    @csrf

                    <div class="space-y-3">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <label class="block font-body text-[10px] font-semibold uppercase text-neutral-500 mb-1">Nama Atribut</label>
                                    <input type="text" :name="'attribute_name[' + index + ']'" x-model="row.name" placeholder="Contoh: Ukuran" required class="w-full h-10 px-3 rounded-md text-sm border border-neutral-200" />
                                </div>
                                <div class="flex-1">
                                    <label class="block font-body text-[10px] font-semibold uppercase text-neutral-500 mb-1">Nilai</label>
                                    <input type="text" :name="'attribute_value[' + index + ']'" x-model="row.value" placeholder="Contoh: 22 cm" required class="w-full h-10 px-3 rounded-md text-sm border border-neutral-200" />
                                </div>
                                <button type="button" @click="removeRow(index)" class="h-10 w-10 shrink-0 rounded-md border border-neutral-200 text-neutral-500 hover:bg-neutral-50 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addRow()" class="font-body text-xs font-semibold text-primary-700 hover:text-primary-800 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Atribut
                    </button>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <x-form.input name="sku" label="SKU (opsional)" />
                        <x-form.input name="price" type="number" step="0.01" min="0" label="Harga Varian" required :value="$product->base_price" />
                        <x-form.input name="stock_quantity" type="number" min="0" label="Stok Awal" required :value="0" />
                    </div>

                    <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer" />
                        <span class="font-body text-sm text-neutral-700">Aktifkan varian</span>
                    </label>

                    <x-button type="submit" variant="primary">Tambah Varian</x-button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.admin>
