<x-layouts.admin>
    <x-slot:title>Tambah Produk</x-slot:title>

    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-primary-700">Tambah Produk</h1>
        <p class="font-body text-sm text-neutral-500 mt-1">Setelah produk dibuat, Anda dapat menambahkan foto dan varian pada halaman berikutnya.</p>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-5">
            @csrf

            <x-form.select
                name="category_id"
                label="Kategori"
                required
                placeholder="Pilih kategori"
                :options="$categories->pluck('name', 'id')"
                :value="old('category_id')"
            />

            <x-form.input name="name" label="Nama Produk" required placeholder="Contoh: Songkok Beludru Hitam" :value="old('name')" />

            <x-form.textarea name="description" label="Deskripsi" placeholder="Deskripsi produk untuk halaman katalog..." :value="old('description')" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form.input name="base_price" type="number" label="Harga Dasar (Rp)" required min="0" step="0.01" :value="old('base_price')" hint="Harga acuan; harga final tiap varian dapat berbeda." />
                <x-form.input name="weight_gram" type="number" label="Berat (gram)" required min="1" :value="old('weight_gram')" hint="Digunakan untuk menghitung ongkos kirim." />
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer" />
                <span class="font-body text-sm text-neutral-700">Aktifkan produk di katalog publik</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Simpan &amp; Lanjutkan</x-button>
                <x-button variant="outlined" :href="route('admin.products.index')">Batal</x-button>
            </div>
        </form>
    </div>
</x-layouts.admin>
