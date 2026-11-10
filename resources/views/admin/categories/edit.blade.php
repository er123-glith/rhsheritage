<x-layouts.admin>
    <x-slot:title>Ubah Kategori</x-slot:title>

    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-primary-700">Ubah Kategori</h1>
        <p class="font-body text-sm text-neutral-500 mt-1">Slug akan diperbarui otomatis bila nama diubah.</p>
    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8 max-w-xl">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-form.input name="name" label="Nama Kategori" required :value="old('name', $category->name)" />

            <x-form.input name="sort_order" type="number" label="Urutan Tampil" hint="Angka lebih kecil tampil lebih dulu." :value="old('sort_order', $category->sort_order)" />

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                <x-button variant="outlined" :href="route('admin.categories.index')">Batal</x-button>
            </div>
        </form>
    </div>
</x-layouts.admin>
