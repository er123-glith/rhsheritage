<x-layouts.public>
    <x-slot:title>Selamat Datang</x-slot:title>

    <div class="text-center py-16 sm:py-24" data-aos="zoom-in" data-aos-duration="700">
        <span class="inline-block px-4 py-1.5 rounded-sm bg-gold-200 text-gold-900 font-body text-xs font-bold uppercase tracking-wider mb-4">
            Tahap 1 Siap &mdash; Fondasi Sistem Selesai
        </span>
        <h1 class="font-display text-4xl sm:text-5xl text-primary-700 font-bold max-w-3xl mx-auto leading-tight">
            Sistem Informasi E-Commerce RHS HERITAGE
        </h1>
        <p class="font-body text-base text-neutral-600 max-w-xl mx-auto mt-4 leading-relaxed">
            Menghadirkan produk kriya berkualitas tinggi sejak 2004 langsung ke genggaman Anda.
        </p>

        <div class="mt-8 flex items-center justify-center gap-4">
            <x-button href="{{ route('admin.login') }}" variant="primary" size="lg">
                Masuk ke Panel Owner
            </x-button>
        </div>
    </div>
</x-layouts.public>
