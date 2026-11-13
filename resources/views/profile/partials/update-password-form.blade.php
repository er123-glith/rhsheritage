<section x-data="{
    showCurrent: false,
    showNew: false,
    showConfirm: false,
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
    <header>
        <h2 class="font-display text-lg font-bold text-neutral-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Perbarui Kata Sandi</span>
        </h2>

        <p class="mt-1 font-body text-xs text-neutral-500 leading-relaxed">
            Pastikan akun Anda terlindungi dengan menggunakan kata sandi yang kuat (minimal 12 karakter) serta berbeda dari kata sandi saat ini.
        </p>
    </header>

    @if (session('status') === 'password-updated')
        <div class="mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2"
             x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Kata sandi akun Anda berhasil diperbarui. Sesi perangkat lain telah dicabut demi keamanan.</span>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4 font-body" @submit="submitting = true">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Kata Sandi Saat Ini <span class="text-danger">*</span>
            </label>
            <div class="relative">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    :type="showCurrent ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi saat ini"
                    class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->updatePassword->has('current_password') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                >
                <button
                    type="button"
                    @click="showCurrent = !showCurrent"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600 transition"
                    title="Tampilkan / sembunyikan kata sandi"
                    tabindex="-1"
                >
                    <svg x-show="!showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </button>
            </div>
            @if ($errors->updatePassword->has('current_password'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('current_password') }}</span>
                </p>
            @endif
        </div>

        <!-- Kata Sandi Baru -->
        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Kata Sandi Baru <span class="text-danger">*</span>
                <span class="text-neutral-400 font-normal ml-1">(Minimal 12 karakter)</span>
            </label>
            <div class="relative">
                <input
                    id="update_password_password"
                    name="password"
                    x-model="newPassword"
                    :type="showNew ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 12 karakter"
                    class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->updatePassword->has('password') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
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

            @if ($errors->updatePassword->has('password'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('password') }}</span>
                </p>
            @endif
        </div>

        <!-- Konfirmasi Kata Sandi Baru -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
            </label>
            <div class="relative">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    :type="showConfirm ? 'text' : 'password'"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                    class="w-full h-11 pl-3.5 pr-10 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border {{ $errors->updatePassword->has('password_confirmation') ? 'border-rose-300 ring-2 ring-rose-500/20' : 'border-neutral-200' }} focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
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
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->updatePassword->first('password_confirmation') }}</span>
                </p>
            @endif
        </div>

        <div class="pt-2 flex items-center gap-3">
            <button
                type="submit"
                :disabled="submitting"
                class="px-5 h-10 rounded-lg bg-primary-700 hover:bg-primary-800 active:bg-primary-900 disabled:opacity-60 text-white font-semibold text-xs tracking-wide shadow-sm hover:shadow transition flex items-center gap-2 cursor-pointer disabled:cursor-not-allowed"
            >
                <svg x-show="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span x-text="submitting ? 'Menyimpan...' : 'Perbarui Kata Sandi'">Perbarui Kata Sandi</span>
            </button>
        </div>
    </form>
</section>
