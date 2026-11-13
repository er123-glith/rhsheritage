<section>
    <header class="pb-4 border-b border-neutral-100">
        <h2 class="font-display text-lg font-bold text-neutral-900">
            Informasi Profil & Alamat
        </h2>
        <p class="font-body text-xs text-neutral-500 mt-1">
            Kelola data diri, nomor kontak, serta alamat utama akun Anda.
        </p>

        @if($user->isAdmin())
            <div class="mt-3 p-3.5 rounded-xl bg-primary-50 border border-primary-200 text-primary-900 font-body text-xs leading-relaxed">
                <strong>Catatan Admin / Owner:</strong> Alamat, kontak WhatsApp, dan email ini digunakan secara otomatis sebagai alamat resmi toko pada kop surat dokumen Invoice Pesanan.
            </div>
        @else
            <div class="mt-3 p-3.5 rounded-xl bg-neutral-50 border border-neutral-200 text-neutral-700 font-body text-xs leading-relaxed">
                <strong>Alamat Utama:</strong> Alamat yang Anda simpan di sini akan digunakan sebagai data default pengiriman saat Anda melakukan checkout belanja.
            </div>
        @endif
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" class="font-body text-xs font-semibold text-neutral-700" />
            <x-text-input 
                id="name" 
                name="name" 
                type="text" 
                class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600" 
                :value="old('name', $user->name)" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="Masukkan nama lengkap Anda"
            />
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <!-- Dua Kolom: Email & Nomor Telepon/WA -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" value="Alamat Email" class="font-body text-xs font-semibold text-neutral-700" />
                <x-text-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600" 
                    :value="old('email', $user->email)" 
                    required 
                    autocomplete="username" 
                    placeholder="nama@email.com"
                />
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-xs text-amber-700">
                            Email Anda belum terverifikasi.
                            <button form="send-verification" class="underline text-xs text-primary-700 hover:text-primary-800 font-semibold ml-1">
                                Kirim ulang email verifikasi
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1.5 font-medium text-xs text-emerald-600">
                                Tautan verifikasi baru telah dikirim ke alamat email Anda.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="phone" value="Nomor WhatsApp / HP" class="font-body text-xs font-semibold text-neutral-700" />
                <x-text-input 
                    id="phone" 
                    name="phone" 
                    type="text" 
                    class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600 font-mono" 
                    :value="old('phone', $user->phone)" 
                    placeholder="Contoh: 085728979999"
                />
                <x-input-error class="mt-1.5" :messages="$errors->get('phone')" />
            </div>
        </div>

        <!-- Alamat Lengkap -->
        <div>
            <x-input-label for="address" value="Alamat Lengkap / Jalan" class="font-body text-xs font-semibold text-neutral-700" />
            <textarea 
                id="address" 
                name="address" 
                rows="3" 
                class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600 font-body placeholder:text-neutral-400"
                placeholder="{{ $user->isAdmin() ? 'Contoh: Griya RHS Heritage, Jl. Budaya Warisan No. 88' : 'Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan' }}"
            >{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-1.5" :messages="$errors->get('address')" />
        </div>

        <!-- Grid 3 Kolom: Kota, Provinsi, Kode Pos -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="city" value="Kota / Kabupaten" class="font-body text-xs font-semibold text-neutral-700" />
                <x-text-input 
                    id="city" 
                    name="city" 
                    type="text" 
                    class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600" 
                    :value="old('city', $user->city)" 
                    placeholder="Contoh: Surakarta"
                />
                <x-input-error class="mt-1.5" :messages="$errors->get('city')" />
            </div>

            <div>
                <x-input-label for="province" value="Provinsi" class="font-body text-xs font-semibold text-neutral-700" />
                <x-text-input 
                    id="province" 
                    name="province" 
                    type="text" 
                    class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600" 
                    :value="old('province', $user->province)" 
                    placeholder="Contoh: Jawa Tengah"
                />
                <x-input-error class="mt-1.5" :messages="$errors->get('province')" />
            </div>

            <div>
                <x-input-label for="postal_code" value="Kode Pos" class="font-body text-xs font-semibold text-neutral-700" />
                <x-text-input 
                    id="postal_code" 
                    name="postal_code" 
                    type="text" 
                    class="mt-1.5 block w-full rounded-lg border-neutral-300 text-sm focus:border-primary-600 focus:ring-primary-600 font-mono" 
                    :value="old('postal_code', $user->postal_code)" 
                    placeholder="Contoh: 57126"
                />
                <x-input-error class="mt-1.5" :messages="$errors->get('postal_code')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-3 border-t border-neutral-100">
            <x-primary-button class="bg-primary-700 hover:bg-primary-800 text-white font-body text-xs font-bold py-2.5 px-5 rounded-lg shadow-xs transition">
                Simpan Perubahan Profil
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg"
                >
                    Perubahan berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>
