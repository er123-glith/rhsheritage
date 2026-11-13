<x-layouts.public>
    <x-slot:title>Profil Saya — RHS HERITAGE</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <div data-aos="fade-down" data-aos-duration="600">
            <nav class="flex items-center gap-2 text-xs font-body text-neutral-500 mb-1">
                <a href="{{ route('home') }}" class="hover:text-primary-700">Beranda</a>
                <span>/</span>
                <span class="text-primary-700 font-semibold">Profil Pengguna</span>
            </nav>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-neutral-800">
                Pengaturan Profil & Akun
            </h1>
            <p class="font-body text-xs sm:text-sm text-neutral-500 mt-1">
                Perbarui data akun dan informasi kontak Anda
            </p>
        </div>

        <div class="space-y-6" data-aos="fade-up">
            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
