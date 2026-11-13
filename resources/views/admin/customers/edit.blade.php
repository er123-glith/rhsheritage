<x-layouts.admin>
    <x-slot:title>Edit Pelanggan — {{ $customer->name }}</x-slot:title>

    <div class="space-y-6 max-w-4xl">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('admin.customers.index') }}" class="hover:text-primary-700 transition">Data Pelanggan</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Edit Profil Pelanggan</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Edit Informasi Pelanggan</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Perbarui informasi kontak dan profil akun pelanggan #{{ $customer->id }}.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(! $customer->trashed())
                    <a href="{{ route('admin.customers.password.edit', $customer) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-semibold transition shadow-xs">
                        <svg class="w-4 h-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Ganti Kata Sandi</span>
                    </a>
                @endif
                <a href="{{ route('admin.customers.show', $customer) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Detail</span>
                </a>
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
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Customer ID & Status Badge -->
                <div class="flex items-center justify-between p-4 bg-canvas-sunken rounded-lg border border-neutral-200">
                    <div>
                        <span class="text-xs text-neutral-500 uppercase font-semibold">ID Pelanggan</span>
                        <p class="font-mono font-bold text-primary-700">#{{ $customer->id }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-neutral-500 uppercase font-semibold block text-right">Status Akun</span>
                        @if($customer->trashed())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                Nonaktif (Soft Deleted)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Field: Nama Lengkap -->
                <div>
                    <label for="name" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $customer->name) }}"
                        required
                        maxlength="255"
                        placeholder="Contoh: Budi Santoso"
                        class="w-full h-11 px-4 rounded-lg font-body text-sm border @error('name') border-red-500 focus:ring-red-200 @else border-neutral-200 focus:ring-primary-200 @enderror focus:outline-none focus:border-primary-700 focus:ring-2 transition"
                    />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 font-body">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field: Alamat Email -->
                <div>
                    <label for="email" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email', $customer->email) }}"
                        required
                        maxlength="255"
                        placeholder="nama@email.com"
                        class="w-full h-11 px-4 rounded-lg font-body text-sm border @error('email') border-red-500 focus:ring-red-200 @else border-neutral-200 focus:ring-primary-200 @enderror focus:outline-none focus:border-primary-700 focus:ring-2 transition"
                    />
                    @error('email')
                        <p class="mt-1 text-xs text-red-600 font-body">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-400 font-body">Email digunakan pelanggan untuk masuk ke dashboard akun.</p>
                </div>

                <!-- Field: Nomor Telepon / WhatsApp -->
                <div>
                    <label for="phone" class="block font-body text-xs font-semibold text-neutral-700 uppercase tracking-wider mb-2">
                        Nomor Telepon / WhatsApp (Opsional)
                    </label>
                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        value="{{ old('phone', $customer->phone) }}"
                        maxlength="25"
                        placeholder="Contoh: 081234567890"
                        class="w-full h-11 px-4 rounded-lg font-body text-sm border @error('phone') border-red-500 focus:ring-red-200 @else border-neutral-200 focus:ring-primary-200 @enderror focus:outline-none focus:border-primary-700 focus:ring-2 transition"
                    />
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 font-body">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-400 font-body">Format: angka, spasi, atau tanda tambah (+) untuk konfirmasi pesanan.</p>
                </div>

                <!-- Security Notice -->
                <div class="p-4 rounded-lg bg-blue-50/60 border border-blue-200 text-xs text-blue-900 leading-relaxed space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Ketentuan Keamanan Data:</span>
                    </p>
                    <p>Hak akses (role) dan kata sandi pelanggan tidak dapat diubah melalui modul ini. Kata sandi hanya dapat direset secara mandiri oleh pelanggan melalui tautan reset password resmi.</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="px-5 py-2.5 rounded-lg border border-neutral-300 font-body text-sm font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-semibold transition shadow-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

