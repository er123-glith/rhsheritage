<x-layouts.customer>
    <x-slot:title>Ajukan Retur Pesanan #{{ $order->order_number }} — RHS HERITAGE</x-slot:title>

    <div class="space-y-6 pb-28" x-data="returnFormHandler()">
        <!-- Header & Breadcrumbs -->
        <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('customer.orders.show', $order->order_number) }}" class="hover:text-primary-700 transition">Pesanan #{{ $order->order_number }}</a>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-neutral-700 font-medium">Formulir Retur</span>
        </nav>

        <div>
            <h1 class="font-display text-2xl font-bold text-primary-700">Pengajuan Retur Barang</h1>
            <p class="font-body text-xs text-neutral-500 mt-1">
                Silakan lengkapi formulir di bawah ini dengan menyertakan bukti fisik dan video unboxing paket asli (maks. 100 MB).
            </p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-body text-xs space-y-1">
                <p class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Gagal Mengirim Pengajuan Retur:</span>
                </p>
                <ul class="list-disc list-inside space-y-0.5 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <template x-if="serverError">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-body text-xs space-y-1">
                <p class="font-bold">Peringatan Server:</p>
                <div x-html="serverError"></div>
            </div>
        </template>

        <form id="returnForm" method="POST" action="{{ route('customer.returns.store') }}" enctype="multipart/form-data" class="space-y-6 font-body" @submit.prevent="submitForm($event)">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            <!-- Order Reference Summary Card -->
            <div class="bg-white rounded-xl border border-neutral-200 p-5 shadow-1 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-neutral-400 tracking-wider">Nomor Pesanan</span>
                    <p class="font-mono font-bold text-base text-primary-800">#{{ $order->order_number }}</p>
                    <p class="text-neutral-500 mt-0.5">Dipesan pada {{ $order->created_at->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-neutral-400 tracking-wider">Status Saat Ini</span>
                    <div class="mt-1"><x-status-badge :status="$order->status" /></div>
                </div>
            </div>

            <!-- 1. Pilih Produk & Kuantitas Retur -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-4">
                <div class="border-b border-neutral-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-base font-bold text-primary-700">1. Pilih Produk yang Diretur</h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Centang produk yang bermasalah dan tentukan jumlah barang yang ingin Anda retur.</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold" :class="hasSelectedItems ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                        <span x-text="selectedCount"></span> terpilih
                    </span>
                </div>

                <div class="divide-y divide-neutral-100">
                    @foreach($order->orderItems as $index => $item)
                        @php
                            $remaining = $item->remainingReturnableQuantity();
                        @endphp
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    id="item_{{ $item->id }}" 
                                    x-model="selectedItems[{{ $item->id }}]"
                                    {{ $remaining <= 0 ? 'disabled' : '' }}
                                    class="w-4 h-4 mt-1 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                                >
                                <div>
                                    <label for="item_{{ $item->id }}" class="font-bold text-sm text-neutral-800 cursor-pointer">
                                        {{ $item->product_name }}
                                    </label>
                                    <p class="text-xs font-mono text-neutral-500">{{ $item->variant_label }}</p>
                                    <p class="text-xs text-neutral-600 mt-1">
                                        Harga: Rp{{ number_format($item->unit_price, 0, ',', '.') }} • Dibeli: {{ $item->quantity }} pcs
                                        @if($remaining < $item->quantity)
                                            <span class="text-amber-600 font-semibold">(Sisa dapat diretur: {{ $remaining }} pcs)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($remaining > 0)
                                <div class="flex items-center gap-2" x-show="selectedItems[{{ $item->id }}]">
                                    <input type="hidden" name="items[{{ $index }}][id]" :disabled="!selectedItems[{{ $item->id }}]" value="{{ $item->id }}">
                                    <label class="text-xs text-neutral-600 font-semibold">Jumlah Retur:</label>
                                    <input 
                                        type="number" 
                                        name="items[{{ $index }}][qty]" 
                                        x-model="itemQuantities[{{ $item->id }}]"
                                        :disabled="!selectedItems[{{ $item->id }}]"
                                        min="1" 
                                        max="{{ $remaining }}" 
                                        class="w-20 h-9 px-2.5 text-center text-xs font-bold rounded-lg border border-neutral-300 focus:border-primary-600 focus:ring-1 focus:ring-primary-600"
                                    >
                                    <span class="text-xs text-neutral-500">pcs (maks. {{ $remaining }})</span>
                                </div>
                            @else
                                <span class="px-2.5 py-1 rounded bg-neutral-100 text-neutral-500 text-xs font-semibold">
                                    Sudah Diretur Penuh
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. Pilih Solusi & Alasan Retur -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-5">
                <div class="border-b border-neutral-100 pb-3">
                    <h2 class="font-display text-base font-bold text-primary-700">2. Solusi & Alasan Pengajuan</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Pilih jenis penyelesaian yang Anda harapkan.</p>
                </div>

                <!-- Solusi: Replacement vs Refund -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 mb-2">
                        Pilihan Solusi <span class="text-danger">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3" :class="solution === 'replacement' ? 'border-primary-600 bg-primary-50/50 ring-2 ring-primary-500/20' : 'border-neutral-200 hover:border-neutral-300'">
                            <input type="radio" name="solution" value="replacement" x-model="solution" class="mt-0.5 text-primary-700 focus:ring-primary-500">
                            <div>
                                <span class="font-bold text-sm text-neutral-800 block">Penggantian Barang (Replacement)</span>
                                <span class="text-xs text-neutral-500 mt-0.5 block">Kirim kembali barang rusak/keliru, kami akan mengirimkan barang baru yang sesuai spesifikasi pesanan Anda.</span>
                            </div>
                        </label>

                        <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3" :class="solution === 'refund' ? 'border-primary-600 bg-primary-50/50 ring-2 ring-primary-500/20' : 'border-neutral-200 hover:border-neutral-300'">
                            <input type="radio" name="solution" value="refund" x-model="solution" class="mt-0.5 text-primary-700 focus:ring-primary-500">
                            <div>
                                <span class="font-bold text-sm text-neutral-800 block">Pengembalian Dana (Refund)</span>
                                <span class="text-xs text-neutral-500 mt-0.5 block">Pengembalian dana sejumlah nilai produk yang diretur ditransfer ke rekening bank Anda setelah verifikasi barang diterima.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Alasan Retur -->
                <div>
                    <label for="reason" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Alasan Retur <span class="text-danger">*</span>
                    </label>
                    <select name="reason" id="reason" x-model="reason" required class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20">
                        <option value="">-- Pilih Alasan Utama --</option>
                        <option value="rusak_cacat">Produk Rusak / Cacat Fisik / Jahitan Rusak</option>
                        <option value="tidak_sesuai">Produk Tidak Sesuai Deskripsi Katalog</option>
                        <option value="salah_varian">Salah Kirim Varian / Ukuran / Warna</option>
                        <option value="jumlah_kurang">Jumlah Barang Kurang dari Pesanan</option>
                        <option value="tidak_lengkap">Aksesoris / Komponen Produk Tidak Lengkap</option>
                        <option value="lainnya">Alasan Lainnya</option>
                    </select>
                </div>

                <!-- Deskripsi Masalah -->
                <div>
                    <label for="description" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Deskripsi Masalah Lengkap <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        name="description" 
                        id="description" 
                        x-model="description"
                        rows="4" 
                        required 
                        placeholder="Jelaskan secara rinci kondisi produk yang Anda terima dan bagian mana yang bermasalah (minimal 10 karakter)..."
                        class="w-full p-3 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20"
                    ></textarea>
                    <p class="text-[11px] text-neutral-400 mt-1" x-text="'Panjang karakter: ' + (description ? description.trim().length : 0) + ' / min. 10'"></p>
                </div>
            </div>

            <!-- 3. Upload Bukti: Video Unboxing Wajib & Foto -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-6">
                <div class="border-b border-neutral-100 pb-3">
                    <h2 class="font-display text-base font-bold text-primary-700">3. Berkas Bukti (Video Unboxing Wajib & Foto)</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Demi transparansi dan keamanan bersama, pengajuan retur standar wajib menyertakan video unboxing dan foto bukti fisik.</p>
                </div>

                <!-- Info Box Ketentuan Video Unboxing -->
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-1">
                        <p class="font-bold">Ketentuan Bukti Video Unboxing RHS Heritage:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-amber-800">
                            <li>Rekam paket dari semua sisi sebelum segel/kemasan dibuka.</li>
                            <li>Tunjukkan pembukaan paket dan fisik produk tanpa jeda/editing.</li>
                            <li>Format didukung: <strong>MP4, MOV, WEBM</strong> (Maksimal <strong>100 MB</strong>).</li>
                            <li><strong>Tidak memiliki video unboxing?</strong> Hubungi Layanan Pelanggan kami via WhatsApp untuk prosedur khusus.</li>
                        </ul>
                    </div>
                </div>

                <!-- Bagian Input Video Unboxing -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="unboxing_video" class="block text-xs font-semibold text-neutral-700">
                            Berkas Video Unboxing <span class="text-danger">*</span>
                            <span class="text-neutral-400 font-normal ml-1">(Format: MP4, MOV, WEBM • Maks. {{ config('uploads.unboxing_video.max_mb', 100) }} MB)</span>
                        </label>
                        <template x-if="videoName">
                            <button type="button" @click="resetVideo()" class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Ganti Video</span>
                            </button>
                        </template>
                    </div>

                    <input 
                        type="file" 
                        name="unboxing_video" 
                        id="unboxing_video" 
                        x-ref="videoInput"
                        required 
                        accept="{{ config('uploads.unboxing_video.accept', 'video/mp4,video/quicktime,video/webm') }}"
                        @change="handleVideoSelect($event)"
                        class="w-full text-xs text-neutral-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                    >

                    <!-- Preview / Status Video -->
                    <template x-if="videoName">
                        <div class="mt-2 p-3.5 rounded-xl border text-xs flex items-center justify-between transition-all"
                            :class="(isVideoOverLimit || isVideoFormatInvalid) ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-emerald-50 border-emerald-300 text-emerald-800'">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="text-base font-bold" x-text="(isVideoOverLimit || isVideoFormatInvalid) ? '⚠️' : '🎬'"></span>
                                <div class="truncate">
                                    <p class="font-semibold truncate" x-text="videoName"></p>
                                    <p class="text-[11px] opacity-80" x-text="'Ukuran: ' + videoSizeFormatted + ' • Maks. 100 MB'"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" 
                                    :class="(isVideoOverLimit || isVideoFormatInvalid) ? 'bg-rose-200 text-rose-900' : 'bg-emerald-200 text-emerald-900'"
                                    x-text="(isVideoOverLimit || isVideoFormatInvalid) ? 'Tidak Valid' : 'Valid (Siap Unggah)'">
                                </span>
                                <button type="button" @click="$refs.videoInput.click()" class="px-2.5 py-1 rounded bg-white border border-neutral-300 text-neutral-700 text-[11px] font-semibold hover:bg-neutral-50 cursor-pointer">
                                    Ganti Berkas
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="videoError">
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="videoError" class="font-medium"></span>
                        </div>
                    </template>
                </div>

                <!-- Bagian Input Foto Bukti Fisik -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="photos" class="block text-xs font-semibold text-neutral-700">
                            Foto Bukti Fisik Kerusakan / Ketidaksesuaian <span class="text-danger">*</span>
                            <span class="text-neutral-400 font-normal ml-1">(Minimal 1 foto • Format: JPG, PNG, WEBP • Maks. {{ config('uploads.return_photo.max_mb', 5) }} MB/foto)</span>
                        </label>
                        <template x-if="hasPhotos">
                            <button type="button" @click="resetPhotos()" class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Ganti Foto</span>
                            </button>
                        </template>
                    </div>

                    <input 
                        type="file" 
                        name="photos[]" 
                        id="photos" 
                        x-ref="photosInput"
                        required 
                        multiple 
                        accept="{{ config('uploads.return_photo.accept', 'image/jpeg,image/png,image/webp') }}"
                        @change="handlePhotosSelect($event)"
                        class="w-full text-xs text-neutral-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                    >

                    <!-- Preview Daftar Foto -->
                    <template x-if="hasPhotos">
                        <div class="mt-2 space-y-1.5">
                            <div class="text-[11px] font-semibold text-neutral-500 flex items-center justify-between">
                                <span x-text="photosList.length + ' foto dipilih:'"></span>
                                <button type="button" @click="$refs.photosInput.click()" class="text-primary-700 hover:underline cursor-pointer">
                                    Pilih Foto Lain / Tambah
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="(photo, pIdx) in photosList" :key="pIdx">
                                    <div class="p-2.5 rounded-lg border text-xs flex items-center justify-between"
                                        :class="(photo.isOver || photo.isInvalidFormat) ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-neutral-50 border-neutral-200 text-neutral-800'">
                                        <div class="flex items-center gap-2 truncate">
                                            <span x-text="(photo.isOver || photo.isInvalidFormat) ? '❌' : '📷'"></span>
                                            <span class="truncate font-medium" x-text="photo.name"></span>
                                            <span class="text-2xs opacity-75 whitespace-nowrap" x-text="'(' + photo.size + ')'"></span>
                                        </div>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                            :class="(photo.isOver || photo.isInvalidFormat) ? 'bg-rose-200 text-rose-900' : 'bg-emerald-100 text-emerald-800'"
                                            x-text="(photo.isOver || photo.isInvalidFormat) ? 'Gagal' : 'OK'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="photosError">
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="photosError" class="font-medium"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 4. Pernyataan & Tombol Submit (Inline Action Area) -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-4">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="agreement" 
                        value="1" 
                        x-model="agreed"
                        required 
                        class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                    >
                    <span class="text-xs text-neutral-700 leading-snug">
                        Saya menyatakan bahwa seluruh informasi dan bukti video/foto yang saya lampirkan adalah benar dan autentik sesuai produk yang saya terima dari RHS Heritage.
                    </span>
                </label>

                <!-- Status Petunjuk Validasi -->
                <div class="p-3 rounded-lg border text-xs flex items-center gap-2"
                    :class="canSubmit() ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-neutral-50 border-neutral-200 text-neutral-600'">
                    <span x-text="canSubmit() ? '✅' : 'ℹ️'"></span>
                    <span x-text="validationHint()" class="font-medium"></span>
                </div>

                <!-- Upload Progress Bar (Aktif Saat Pengiriman Berjalan) -->
                <template x-if="submitting">
                    <div class="space-y-1.5 p-3 rounded-lg bg-primary-50 border border-primary-200">
                        <div class="flex items-center justify-between text-xs font-semibold text-primary-900">
                            <span>Mengunggah berkas pengajuan retur (video & foto)...</span>
                            <span x-text="uploadProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-primary-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-primary-700 h-2.5 rounded-full transition-all duration-200" :style="'width: ' + uploadProgress + '%'"></div>
                        </div>
                        <p class="text-[11px] text-primary-700">Mohon jangan menutup halaman ini hingga proses unggah selesai.</p>
                    </div>
                </template>

                <!-- Tombol Submit Form Utama -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        :disabled="!canSubmit()" 
                        :class="!canSubmit() ? 'opacity-50 cursor-not-allowed bg-neutral-400' : 'bg-primary-700 hover:bg-primary-800 cursor-pointer shadow-md'"
                        class="w-full sm:w-auto px-6 py-3 rounded-lg text-white font-bold text-xs shadow-1 transition flex items-center justify-center gap-2"
                    >
                        <svg x-show="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="submitting ? ('Mengunggah Berkas (' + uploadProgress + '%)...') : ('Kirim Pengajuan Retur (' + (solution === 'replacement' ? 'Replacement' : 'Refund') + ')')">
                            Kirim Pengajuan Retur
                        </span>
                    </button>

                    <a href="{{ route('customer.orders.show', $order->order_number) }}" class="w-full sm:w-auto text-center px-4 py-3 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        Batal
                    </a>
                </div>
            </div>
        </form>

        <!-- STICKY ACTION FOOTER BAR (Selalu terlihat di bagian bawah layar tanpa menutupi sidebar) -->
        <div class="fixed bottom-0 md:left-72 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-neutral-200/90 shadow-3 px-4 py-3 sm:px-6 transition-all duration-200">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
                <!-- Info Status Form -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" :class="canSubmit() ? 'bg-emerald-500 animate-pulse' : 'bg-amber-400'"></span>
                        <span class="font-body text-xs font-semibold text-neutral-800" x-text="canSubmit() ? 'Formulir siap dikirim' : 'Lengkapi formulir retur'"></span>
                    </div>
                    <span class="text-xs text-neutral-400 hidden sm:inline">•</span>
                    <span class="text-xs text-neutral-600 font-medium">
                        Solusi: <strong class="text-primary-800" x-text="solution === 'replacement' ? 'Penggantian (Replacement)' : 'Pengembalian Dana (Refund)'"></strong>
                    </span>
                </div>

                <!-- Tombol Action Sticky -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <a href="{{ route('customer.orders.show', $order->order_number) }}" class="px-3.5 py-2 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        Batal
                    </a>
                    <button 
                        type="submit" 
                        form="returnForm"
                        :disabled="!canSubmit()" 
                        :class="!canSubmit() ? 'opacity-50 cursor-not-allowed bg-neutral-400' : 'bg-primary-700 hover:bg-primary-800 cursor-pointer shadow-md'"
                        class="px-5 py-2 rounded-lg text-white font-bold text-xs shadow-1 transition flex items-center gap-2"
                    >
                        <svg x-show="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="submitting ? ('Mengunggah (' + uploadProgress + '%)...') : ('Kirim Pengajuan Retur')">Kirim Pengajuan Retur</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function returnFormHandler() {
            return {
                submitting: false,
                uploadProgress: 0,
                serverError: '',
                solution: '{{ old('solution', 'replacement') }}',
                reason: '{{ old('reason', '') }}',
                description: {!! json_encode(old('description', '')) !!},
                agreed: false,

                // Data Video Unboxing
                videoName: '',
                videoSizeFormatted: '',
                videoError: '',
                isVideoOverLimit: false,
                isVideoFormatInvalid: false,
                maxVideoBytes: {{ config('uploads.unboxing_video.max_bytes', 104857600) }},
                maxVideoMb: {{ config('uploads.unboxing_video.max_mb', 100) }},
                allowedVideoExts: ['mp4', 'mov', 'webm'],
                allowedVideoMimes: ['video/mp4', 'video/quicktime', 'video/webm'],

                // Data Foto Bukti Fisik
                photosList: [],
                photosError: '',
                isPhotosOverLimit: false,
                isPhotosFormatInvalid: false,
                maxPhotoBytes: {{ config('uploads.return_photo.max_bytes', 5242880) }},
                maxPhotoMb: {{ config('uploads.return_photo.max_mb', 5) }},
                allowedPhotoExts: ['jpg', 'jpeg', 'png', 'webp'],
                allowedPhotoMimes: ['image/jpeg', 'image/png', 'image/webp'],

                // Item Produk Terpilih & Kuantitas
                selectedItems: {
                    @foreach($order->orderItems as $item)
                        @if($item->remainingReturnableQuantity() > 0)
                            {{ $item->id }}: true,
                        @endif
                    @endforeach
                },
                itemQuantities: {
                    @foreach($order->orderItems as $item)
                        {{ $item->id }}: 1,
                    @endforeach
                },

                get hasSelectedItems() {
                    return Object.values(this.selectedItems).some(val => Boolean(val));
                },

                get selectedCount() {
                    return Object.values(this.selectedItems).filter(val => Boolean(val)).length;
                },

                get hasVideo() {
                    return Boolean(this.videoName) && !this.isVideoOverLimit && !this.isVideoFormatInvalid;
                },

                get hasPhotos() {
                    return this.photosList.length !== 0 && !this.isPhotosOverLimit && !this.isPhotosFormatInvalid;
                },

                handleVideoSelect(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        this.resetVideo();
                        return;
                    }
                    this.videoName = file.name;
                    const sizeMb = (file.size / 1048576).toFixed(1);
                    this.videoSizeFormatted = sizeMb + ' MB';

                    const ext = file.name.split('.').pop().toLowerCase();
                    const mime = file.type ? file.type.toLowerCase() : '';

                    // Validasi format video
                    if (!this.allowedVideoExts.includes(ext) && !this.allowedVideoMimes.some(m => mime.includes(m))) {
                        this.isVideoFormatInvalid = true;
                        this.isVideoOverLimit = false;
                        this.videoError = 'Format berkas "' + ext.toUpperCase() + '" tidak sah. Format yang diperbolehkan: MP4, MOV, atau WEBM.';
                        return;
                    }
                    this.isVideoFormatInvalid = false;

                    // Validasi ukuran video
                    if (file.size > this.maxVideoBytes) {
                        this.isVideoOverLimit = true;
                        this.videoError = 'Video melebihi batas maksimal ' + this.maxVideoMb + ' MB (Ukuran berkas: ' + this.videoSizeFormatted + '). Silakan kompres video atau pilih file lain.';
                    } else {
                        this.isVideoOverLimit = false;
                        this.videoError = '';
                    }
                },

                resetVideo() {
                    this.videoName = '';
                    this.videoSizeFormatted = '';
                    this.videoError = '';
                    this.isVideoOverLimit = false;
                    this.isVideoFormatInvalid = false;
                    if (this.$refs.videoInput) {
                        this.$refs.videoInput.value = '';
                    }
                },

                handlePhotosSelect(e) {
                    const files = Array.from(e.target.files);
                    this.photosList = [];
                    this.photosError = '';
                    this.isPhotosOverLimit = false;
                    this.isPhotosFormatInvalid = false;

                    if (!files.length) return;

                    for (const file of files) {
                        const sizeKb = file.size / 1024;
                        const formatted = sizeKb < 1024 ? sizeKb.toFixed(1) + ' KB' : (sizeKb / 1024).toFixed(2) + ' MB';
                        const ext = file.name.split('.').pop().toLowerCase();
                        const mime = file.type ? file.type.toLowerCase() : '';

                        const isInvalidFormat = !this.allowedPhotoExts.includes(ext) && !this.allowedPhotoMimes.some(m => mime.includes(m));
                        const isOver = file.size > this.maxPhotoBytes;

                        if (isInvalidFormat) {
                            this.isPhotosFormatInvalid = true;
                            this.photosError = 'Foto "' + file.name + '" memiliki format tidak sah. Gunakan format JPG, JPEG, PNG, atau WEBP.';
                        } else if (isOver && !this.photosError) {
                            this.isPhotosOverLimit = true;
                            this.photosError = 'Foto "' + file.name + '" (' + formatted + ') melebihi batas maksimal ' + this.maxPhotoMb + ' MB.';
                        }

                        this.photosList.push({
                            name: file.name,
                            size: formatted,
                            isOver: isOver,
                            isInvalidFormat: isInvalidFormat
                        });
                    }
                },

                resetPhotos() {
                    this.photosList = [];
                    this.photosError = '';
                    this.isPhotosOverLimit = false;
                    this.isPhotosFormatInvalid = false;
                    if (this.$refs.photosInput) {
                        this.$refs.photosInput.value = '';
                    }
                },

                canSubmit() {
                    if (this.submitting) return false;
                    if (!this.hasSelectedItems) return false;
                    if (!this.solution) return false;
                    if (!this.reason) return false;
                    if (!this.description || this.description.trim().length < 10) return false;
                    if (!this.hasVideo) return false;
                    if (!this.hasPhotos) return false;
                    if (!this.agreed) return false;
                    return true;
                },

                validationHint() {
                    if (!this.hasSelectedItems) return 'Pilih minimal satu produk yang ingin diretur.';
                    if (!this.reason) return 'Pilih alasan retur.';
                    if (!this.description || this.description.trim().length < 10) return 'Tuliskan deskripsi masalah minimal 10 karakter.';
                    if (!this.videoName) return 'Wajib memilih berkas video unboxing (maks. 100 MB).';
                    if (this.isVideoOverLimit) return 'Video melebihi 100 MB. Silakan kompres video terlebih dahulu.';
                    if (this.isVideoFormatInvalid) return 'Format video tidak didukung (gunakan MP4, MOV, atau WEBM).';
                    if (this.photosList.length === 0) return 'Wajib memilih minimal satu foto bukti fisik.';
                    if (this.isPhotosOverLimit) return 'Ada foto yang melebihi batas 5 MB.';
                    if (this.isPhotosFormatInvalid) return 'Format foto tidak didukung (gunakan JPG, PNG, atau WEBP).';
                    if (!this.agreed) return 'Centang pernyataan persetujuan keaslian data retur.';
                    return 'Semua persyaratan terpenuhi. Formulir siap dikirim.';
                },

                submitForm(e) {
                    if (!this.canSubmit()) {
                        e.preventDefault();
                        return false;
                    }

                    if (this.submitting) {
                        e.preventDefault();
                        return false;
                    }

                    this.submitting = true;
                    this.uploadProgress = 0;
                    this.serverError = '';

                    const form = document.getElementById('returnForm');
                    const formData = new FormData(form);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json, text/html, */*');

                    xhr.upload.onprogress = (progressEvent) => {
                        if (progressEvent.lengthComputable) {
                            const percent = Math.min(99, Math.round((progressEvent.loaded / progressEvent.total) * 100));
                            this.uploadProgress = percent;
                        }
                    };

                    xhr.onload = () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            this.uploadProgress = 100;
                            try {
                                const res = JSON.parse(xhr.responseText);
                                if (res.redirect) {
                                    window.location.href = res.redirect;
                                    return;
                                }
                            } catch (err) {}
                            if (xhr.responseURL) {
                                window.location.href = xhr.responseURL;
                            } else {
                                window.location.href = '{{ route("customer.returns.index") }}';
                            }
                        } else if (xhr.status === 413) {
                            this.submitting = false;
                            this.uploadProgress = 0;
                            this.serverError = 'Ukuran berkas melebihi batas maksimal server (100 MB). Silakan kompres video atau pilih berkas yang lebih kecil.';
                            if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Ukuran Berkas Terlalu Besar (Error 413)',
                                    text: this.serverError,
                                    confirmButtonColor: '#C0392B'
                                });
                            }
                        } else if (xhr.status === 422) {
                            this.submitting = false;
                            this.uploadProgress = 0;
                            try {
                                const data = JSON.parse(xhr.responseText);
                                const errors = data.errors ? Object.values(data.errors).flat() : [data.message || 'Validasi pengajuan gagal.'];
                                this.serverError = errors.join('<br>');
                                if (window.Swal) {
                                    window.Swal.fire({
                                        icon: 'error',
                                        title: 'Validasi Pengajuan Gagal',
                                        html: errors.map(e => `<p class="text-xs mb-1">• ${e}</p>`).join(''),
                                        confirmButtonColor: '#C0392B'
                                    });
                                }
                            } catch (err) {
                                this.serverError = 'Terjadi kesalahan validasi data.';
                            }
                        } else {
                            this.submitting = false;
                            this.uploadProgress = 0;
                            this.serverError = 'Terjadi kesalahan pada server saat memproses retur (Kode: ' + xhr.status + '). Silakan coba lagi.';
                            if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Mengirim Pengajuan',
                                    text: this.serverError,
                                    confirmButtonColor: '#C0392B'
                                });
                            }
                        }
                    };

                    xhr.onerror = () => {
                        this.submitting = false;
                        this.uploadProgress = 0;
                        this.serverError = 'Koneksi terputus saat mengunggah berkas retur. Periksa koneksi internet Anda dan coba lagi.';
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'error',
                                title: 'Koneksi Terputus',
                                text: this.serverError,
                                confirmButtonColor: '#C0392B'
                            });
                        }
                    };

                    xhr.send(formData);
                }
            };
        }
    </script>
</x-layouts.customer>
