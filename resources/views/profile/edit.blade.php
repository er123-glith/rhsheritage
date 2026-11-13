@php
    $layoutComponent = auth()->user()?->isAdmin() ? 'layouts.admin' : 'layouts.customer';
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot:title>Profil Saya — RHS HERITAGE</x-slot:title>

    <div class="space-y-6">
        <div>
            <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                <a href="{{ auth()->user()?->isAdmin() ? route('admin.dashboard') : route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-700 font-medium">Pengaturan Profil & Akun</span>
            </nav>
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">
                Pengaturan Profil & Akun
            </h1>
            <p class="font-body text-sm text-neutral-500 mt-1">
                Perbarui informasi akun, alamat email, dan kata sandi Anda.
            </p>
        </div>

        <div class="space-y-6">
            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-neutral-200 shadow-1 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
