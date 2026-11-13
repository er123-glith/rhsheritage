<x-guest-layout>
    <x-slot:title>Masuk ke Akun — RHS HERITAGE</x-slot:title>

    <div class="mb-6 text-center">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-800">
            Masuk ke Akun
        </h2>
        <p class="font-body text-xs text-neutral-500 mt-1">
            Panel Masuk Owner & Pelanggan RHS HERITAGE
        </p>
    </div>

    <!-- Alert Khusus Proteksi Keranjang (Section D) -->
    @if (session('cart_auth_notice'))
        <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm font-body leading-relaxed flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-amber-900">
                    {{ session('cart_auth_notice') }}
                </p>
                <p class="mt-1 text-xs text-amber-700">
                    Belum memiliki akun?
                    <a href="{{ route('register') }}" class="font-bold underline hover:text-amber-950 ml-1">
                        Daftar Akun Baru &rarr;
                    </a>
                </p>
            </div>
        </div>
    @endif

    <!-- Status Session (Misal: Logout, Reset Password) -->
    @if (session('status'))
        <div class="mb-5 p-3.5 rounded-xl bg-primary-50 border border-primary-200 text-primary-900 text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    <!-- Alert Kesalahan Validasi Global -->
    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-body space-y-1">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Gagal Masuk:</span>
            </p>
            <ul class="list-disc list-inside space-y-0.5 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4 font-body">
        @csrf

        <!-- Email -->
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
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Kata Sandi <span class="text-danger">*</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
            <p class="mt-1.5 text-[11px] text-neutral-500 font-body leading-normal">
                Perubahan kata sandi dapat dilakukan melalui Profil Saya setelah Anda masuk. Apabila mengalami kendala akses akun, silakan hubungi Administrator RHS Heritage.
            </p>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember_me"
                    class="w-4 h-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                >
                <span class="text-xs text-neutral-600">Ingat Saya di Perangkat Ini</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button
                type="submit"
                class="w-full h-11 rounded-lg bg-primary-700 hover:bg-primary-800 active:bg-primary-900 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>

    <!-- Switch to Register -->
    <div class="mt-6 pt-5 border-t border-neutral-100 text-center font-body text-xs text-neutral-600">
        <span>Belum memiliki akun?</span>
        <a href="{{ route('register') }}" class="text-primary-700 hover:text-primary-800 font-bold hover:underline ml-1">
            Daftar Akun Pelanggan
        </a>
    </div>
</x-guest-layout>
