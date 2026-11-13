<x-guest-layout>
    <x-slot:title>Daftar Akun Pelanggan — RHS HERITAGE</x-slot:title>

    <div class="mb-6 text-center">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-800">
            Daftar Akun Pelanggan
        </h2>
        <p class="font-body text-xs text-neutral-500 mt-1">
            Buat akun untuk kemudahan belanja kriya dan melacak pesanan Anda
        </p>
    </div>

    <!-- Alert Kesalahan Validasi Global -->
    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-body space-y-1">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Periksa Kembali Data Anda:</span>
            </p>
            <ul class="list-disc list-inside space-y-0.5 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4 font-body">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Nama Lengkap <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Contoh: Budi Santoso"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Alamat Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                placeholder="nama@email.com"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Kata Sandi -->
        <div>
            <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Kata Sandi <span class="text-danger">*</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Konfirmasi Kata Sandi <span class="text-danger">*</span>
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi kata sandi"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full h-11 rounded-lg bg-primary-700 hover:bg-primary-800 active:bg-primary-900 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Daftar Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>

    <!-- Switch to Login -->
    <div class="mt-6 pt-5 border-t border-neutral-100 text-center font-body text-xs text-neutral-600">
        <span>Sudah memiliki akun?</span>
        <a href="{{ route('login') }}" class="text-primary-700 hover:text-primary-800 font-bold hover:underline ml-1">
            Masuk di Sini
        </a>
    </div>
</x-guest-layout>
