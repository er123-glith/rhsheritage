<x-layouts.admin>
    <x-slot:title>Tambah Kategori</x-slot:title>

    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-primary-700">Tambah Kategori</h1>
        <p class="font-body text-sm text-neutral-500 mt-1">Kategori digunakan untuk mengelompokkan produk pada katalog publik.</p>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8 max-w-xl">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-5">
            @csrf

            <x-form.input name="name" label="Nama Kategori" required placeholder="Contoh: Songkok" :value="old('name')" />

            <x-form.input name="sort_order" type="number" label="Urutan Tampil" hint="Angka lebih kecil tampil lebih dulu." :value="old('sort_order', 0)" />

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Simpan Kategori</x-button>
                <x-button variant="outlined" :href="route('admin.categories.index')">Batal</x-button>
            </div>
        </form>
    </div>
</x-layouts.admin>
