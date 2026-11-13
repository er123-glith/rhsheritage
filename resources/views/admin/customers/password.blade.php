<x-layouts.admin>
    <x-slot:title>Ganti Kata Sandi Pelanggan — {{ $customer->name }}</x-slot:title>

    <div class="space-y-6" x-data="{
        showNew: false,
        showConfirm: false,
        showAdmin: false,
        submitting: false,
        newPassword: '',
        get strength() {
            let p = this.newPassword || '';
            if (!p) return { score: 0, text: '', color: 'bg-neutral-200' };
            let score = 0;
            if (p.length >= 12) score++;
            if (p.length >= 16) score++;
            if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score++;
            if (/\d/.test(p)) score++;
            if (/[^A-Za-z0-9]/.test(p)) score++;

            if (score <= 1) return { score: 1, text: 'Sangat Lemah (Min. 12 karakter)', color: 'bg-rose-500', width: '20%' };
            if (score === 2) return { score: 2, text: 'Lemah', color: 'bg-amber-500', width: '40%' };
            if (score === 3) return { score: 3, text: 'Cukup', color: 'bg-yellow-500', width: '60%' };
            if (score === 4) return { score: 4, text: 'Kuat', color: 'bg-emerald-500', width: '80%' };
            return { score: 5, text: 'Sangat Kuat', color: 'bg-emerald-600', width: '100%' };
        }
    }">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <nav class="flex items-center gap-2 font-body text-xs text-neutral-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('admin.customers.index') }}" class="hover:text-primary-700 transition">Data Pelanggan</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="hover:text-primary-700 transition">{{ $customer->name }}</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Ganti Kata Sandi</span>
                </nav>
                <h1 class="font-display text-2xl font-bold text-neutral-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Ganti Kata Sandi Pelanggan</span>
                </h1>
                <p class="font-body text-xs text-neutral-500 mt-1">
                    Atur ulang kata sandi untuk akun {{ $customer->name }} (#{{ $customer->id }}). Tindakan ini memerlukan verifikasi kata sandi Administrator Anda.
                </p>
            </div>

            <div>
                <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Profil</span>
                </a>
            </div>
        </div>

        <!-- Security Warning Banner -->
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 font-body text-xs leading-relaxed flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="space-y-1">
                <p class="font-bold text-amber-900">Perhatian Keamanan & Integritas Sesi</p>
                <ul class="list-disc list-inside space-y-0.5 text-amber-800">
                    <li>Setelah kata sandi diganti, seluruh sesi login aktif milik pelanggan pada perangkat lain akan otomatis dicabut.</li>
                    <li>Tindakan ini akan tercatat dalam <strong>Audit Log Administrator</strong> lengkap dengan waktu dan alamat IP Anda.</li>
                    <li>Pastikan Anda telah mengonfirmasi permintaan perubahan kata sandi langsung dengan pemilik akun bersangkutan.</li>
                </ul>
            </div>
        </div>

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 font-body text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.customers.password.update', $customer) }}" class="space-y-6" @submit="submitting = true">
                @csrf
                @method('PUT')

                <!-- Customer Identity (Read-only) -->
                <div class="p-4 bg-canvas-sunken rounded-lg border border-neutral-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider">Nama Pelanggan</span>
                        <span class="text-sm font-bold text-neutral-800">{{ $customer->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider">Alamat Email</span>
                        <span class="text-sm font-medium text-neutral-700">{{ $customer->email }}</span>
                    </div>
                </div>

                <div class="border-t border-neutral-100 pt-4 space-y-5">
                    <!-- Kata Sandi Baru Pelanggan -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                            Kata Sandi Baru Pelanggan <span class="text-danger">*</span>
                            <span class="text-neutral-400 font-normal ml-1">(Minimal 12 karakter)</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                x-model="newPassword"
                                :type="showNew ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan minimal 12 karakter"
                                class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->has('password') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            >
                            <button
                                type="button"
                                @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600 transition"
                                title="Tampilkan / sembunyikan kata sandi"
                                tabindex="-1"
                            >
                                <svg x-show="!showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>

                        <!-- Indikator Kekuatan Sandi -->
                        <div x-show="newPassword.length > 0" class="mt-2 space-y-1" x-transition>
                            <div class="h-1.5 w-full bg-neutral-200 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-300" :class="strength.color" :style="{ width: strength.width }"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-neutral-500">
                                <span>Kekuatan: <strong class="text-neutral-700" x-text="strength.text"></strong></span>
                                <span x-show="newPassword.length < 12" class="text-rose-500 font-medium">Kurang <span x-text="12 - newPassword.length"></span> karakter</span>
                            </div>
                        </div>

                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi kata sandi baru"
                                class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->has('password_confirmation') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            >
                            <button
                                type="button"
                                @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600 transition"
                                title="Tampilkan / sembunyikan kata sandi"
                                tabindex="-1"
                            >
                                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Verifikasi Kata Sandi Admin -->
                    <div class="pt-4 border-t border-neutral-100">
                        <label for="admin_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                            Kata Sandi Administrator Anda <span class="text-danger">*</span>
                            <span class="text-neutral-400 font-normal ml-1">(Verifikasi keamanan identitas)</span>
                        </label>
                        <div class="relative">
                            <input
                                id="admin_password"
                                type="password"
                                name="admin_password"
                                :type="showAdmin ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi akun Admin Anda"
                                class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->has('admin_password') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            >
                            <button
                                type="button"
                                @click="showAdmin = !showAdmin"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600 transition"
                                title="Tampilkan / sembunyikan kata sandi"
                                tabindex="-1"
                            >
                                <svg x-show="!showAdmin" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showAdmin" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1.5 text-[11px] text-neutral-500 font-body">
                            Masukkan kata sandi yang Anda gunakan untuk masuk ke panel Administrator RHS Heritage.
                        </p>
                        @error('admin_password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="px-5 h-11 rounded-lg bg-primary-700 hover:bg-primary-800 active:bg-primary-900 disabled:opacity-60 text-white font-semibold text-xs tracking-wide shadow-sm hover:shadow transition flex items-center gap-2 cursor-pointer disabled:cursor-not-allowed"
                    >
                        <svg x-show="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="submitting ? 'Memproses...' : 'Simpan Kata Sandi Baru'">Simpan Kata Sandi Baru</span>
                    </button>

                    <a href="{{ route('admin.customers.show', $customer) }}" class="px-4 h-11 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition flex items-center justify-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
