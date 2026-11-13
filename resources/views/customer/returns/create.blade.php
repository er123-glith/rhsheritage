<x-layouts.customer>
    <x-slot:title>Ajukan Retur Pesanan #{{ $order->order_number }} — RHS HERITAGE</x-slot:title>

    <div class="space-y-6" x-data="{ submitting: false, solution: 'replacement' }">
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
                Silakan lengkapi formulir di bawah ini dengan menyertakan bukti fisik dan video unboxing paket asli.
            </p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-body text-xs space-y-1">
                <p class="font-bold">Gagal Mengirim Pengajuan Retur:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.returns.store') }}" enctype="multipart/form-data" class="space-y-6 font-body" @submit="submitting = true">
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

            <!-- Pilih Produk & Kuantitas Retur -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-4">
                <div class="border-b border-neutral-100 pb-3">
                    <h2 class="font-display text-base font-bold text-primary-700">1. Pilih Produk yang Diretur</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Centang produk yang bermasalah dan tentukan jumlah barang yang ingin Anda retur.</p>
                </div>

                <div class="divide-y divide-neutral-100">
                    @foreach($order->orderItems as $index => $item)
                        @php
                            $remaining = $item->remainingReturnableQuantity();
                        @endphp
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{ checked: {{ $remaining > 0 ? 'true' : 'false' }}, qty: 1 }">
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    id="item_{{ $item->id }}" 
                                    x-model="checked"
                                    {{ $remaining <= 0 ? 'disabled' : '' }}
                                    class="w-4 h-4 mt-1 rounded border-neutral-300 text-primary-700 focus:ring-primary-500"
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
                                <div class="flex items-center gap-2" x-show="checked">
                                    <input type="hidden" name="items[{{ $index }}][id]" :disabled="!checked" value="{{ $item->id }}">
                                    <label class="text-xs text-neutral-600 font-semibold">Jumlah Retur:</label>
                                    <input 
                                        type="number" 
                                        name="items[{{ $index }}][qty]" 
                                        x-model="qty"
                                        :disabled="!checked"
                                        min="1" 
                                        max="{{ $remaining }}" 
                                        class="w-20 h-9 px-2.5 text-center text-xs font-bold rounded-lg border border-neutral-300 focus:border-primary-600 focus:ring-1 focus:ring-primary-600"
                                    >
                                    <span class="text-xs text-neutral-500">pcs</span>
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

            <!-- Pilih Solusi & Alasan Retur -->
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
                                <span class="text-xs text-neutral-500 mt-0.5 block">Pengembalian dana sejumlah nilai produk yang diretur ditransfer ke rekening bank Anda setelah verifikasi.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Alasan Retur -->
                <div>
                    <label for="reason" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Alasan Retur <span class="text-danger">*</span>
                    </label>
                    <select name="reason" id="reason" required class="w-full h-11 px-3.5 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20">
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
                        rows="4" 
                        required 
                        placeholder="Jelaskan secara rinci kondisi produk yang Anda terima dan bagian mana yang bermasalah..."
                        class="w-full p-3 rounded-lg bg-canvas-sunken text-sm text-neutral-800 border border-neutral-200 focus:bg-white focus:border-primary-600 focus:ring-2 focus:ring-primary-500/20"
                    >{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Upload Bukti: Video Unboxing Wajib & Foto -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-5">
                <div class="border-b border-neutral-100 pb-3">
                    <h2 class="font-display text-base font-bold text-primary-700">3. Berkas Bukti (Video Unboxing Wajib & Foto)</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Demi transparansi dan keamanan bersama, pengajuan retur standar wajib menyertakan video unboxing.</p>
                </div>

                <!-- Security Information Box for Video Unboxing -->
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="space-y-1">
                        <p class="font-bold">Ketentuan Video Unboxing:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-amber-800">
                            <li>Rekam kondisi paket dari semua sisi sebelum segel/kemasan dibuka.</li>
                            <li>Tunjukkan proses pembukaan paket dan kondisi fisik produk tanpa jeda/editing.</li>
                            <li>Format yang didukung: <strong>MP4, MOV, atau WEBM</strong> (Maksimal 100 MB).</li>
                            <li><strong>Tidak memiliki video unboxing?</strong> Pengajuan standar memerlukan video unboxing. Silakan hubungi Customer Service kami via WhatsApp untuk prosedur peninjauan khusus.</li>
                        </ul>
                    </div>
                </div>

                <!-- Video Unboxing Input -->
                <div>
                    <label for="unboxing_video" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Berkas Video Unboxing <span class="text-danger">*</span>
                        <span class="text-neutral-400 font-normal ml-1">(Format: MP4, MOV, WEBM • Maks. 100 MB)</span>
                    </label>
                    <input 
                        type="file" 
                        name="unboxing_video" 
                        id="unboxing_video" 
                        required 
                        accept="video/mp4,video/quicktime,video/webm"
                        class="w-full text-xs text-neutral-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                    >
                </div>

                <!-- Foto Bukti Fisik Input -->
                <div>
                    <label for="photos" class="block text-xs font-semibold text-neutral-700 mb-1.5">
                        Foto Bukti Fisik Kerusakan / Ketidaksesuaian <span class="text-danger">*</span>
                        <span class="text-neutral-400 font-normal ml-1">(Minimal 1 foto • Format: JPG, PNG, WEBP • Maks. 5 MB/foto)</span>
                    </label>
                    <input 
                        type="file" 
                        name="photos[]" 
                        id="photos" 
                        required 
                        multiple 
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs text-neutral-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer"
                    >
                </div>
            </div>

            <!-- Pernyataan & Tombol Submit -->
            <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 space-y-4">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="agreement" 
                        value="1" 
                        required 
                        class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-700 focus:ring-primary-500 cursor-pointer"
                    >
                    <span class="text-xs text-neutral-700 leading-snug">
                        Saya menyatakan bahwa seluruh informasi dan bukti video/foto yang saya lampirkan adalah benar dan autentik sesuai produk yang saya terima dari RHS Heritage.
                    </span>
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        :disabled="submitting" 
                        class="px-6 py-2.5 rounded-lg bg-primary-700 hover:bg-primary-800 disabled:opacity-60 text-white font-bold text-xs shadow-1 transition flex items-center gap-2 cursor-pointer disabled:cursor-not-allowed"
                    >
                        <svg x-show="submitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="submitting ? 'Mengunggah Berkas...' : 'Kirim Pengajuan Retur'">Kirim Pengajuan Retur</span>
                    </button>

                    <a href="{{ route('customer.orders.show', $order->order_number) }}" class="px-4 py-2.5 rounded-lg border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-layouts.customer>
