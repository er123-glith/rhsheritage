<x-guest-layout>
    <x-slot:title>Lupa Kata Sandi — RHS HERITAGE</x-slot:title>

    <div class="mb-6 text-center">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-neutral-800">
            Pemulihan Kata Sandi
        </h2>
        <p class="font-body text-xs text-neutral-500 mt-1">
            Masukkan alamat email yang terdaftar untuk menerima tautan atur ulang kata sandi.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-5 p-3.5 rounded-xl bg-primary-50 border border-primary-200 text-primary-900 text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

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

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4 font-body">
        @csrf

        <!-- Email Address -->
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
                placeholder="nama@email.com"
                class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
            >
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full h-11 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>Kirim Tautan Atur Ulang</span>
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-neutral-100 text-center font-body text-xs text-neutral-600">
        <a href="{{ route('login') }}" class="text-primary-700 hover:text-primary-800 font-bold hover:underline">
            &larr; Kembali ke Halaman Masuk
        </a>
    </div>
</x-guest-layout>
