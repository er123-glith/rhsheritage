<x-guest-layout>
    <x-slot:title>Atur Ulang Kata Sandi — RHS HERITAGE</x-slot:title>

    <div class="mb-6 text-center">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-800">
            Atur Ulang Kata Sandi
        </h2>
        <p class="font-body text-xs text-neutral-500 mt-1">
            Masukkan kata sandi baru untuk akun Anda
        </p>
    </div>

    <!-- Alert Kesalahan Validasi Global -->
    @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-body space-y-1">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4 font-body">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Alamat Email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Kata Sandi Baru <span class="text-danger">*</span>
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

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi kata sandi baru"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full h-11 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Simpan Kata Sandi Baru</span>
            </button>
        </div>
    </form>
</x-guest-layout>
