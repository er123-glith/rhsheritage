<x-layouts.customer>
    <x-slot:title>Tulis Ulasan Toko — Pesanan #{{ $order->order_number }}</x-slot:title>

    <div class="space-y-6 pb-20 max-w-3xl mx-auto" x-data="{
        rating: {{ old('rating', 5) }},
        reviewText: {{ json_encode(old('review', '')) }},
        maxChars: 1000,
        get ratingLabel() {
            switch(parseInt(this.rating)) {
                case 1: return 'Sangat Kecewa (★☆☆☆☆)';
                case 2: return 'Kurang Puas (★★☆☆☆)';
                case 3: return 'Cukup Puas (★★★☆☆)';
                case 4: return 'Puas (★★★★☆)';
                case 5: return 'Sangat Puas (★★★★★)';
                default: return 'Pilih Rating Bintang';
            }
        }
    }">
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('customer.orders') }}" class="hover:text-primary-700 transition">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('customer.orders.show', $order->order_number) }}" class="hover:text-primary-700 transition">#{{ $order->order_number }}</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-neutral-700 dark:text-neutral-300 font-medium">Beri Ulasan Toko</span>
        </nav>

        <!-- Header -->
        <div class="bg-white dark:bg-[#15221d] rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 shadow-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gold-50 dark:bg-gold-950/60 text-gold-900 dark:text-gold-300 border border-gold-300/80 font-body text-xs font-bold mb-2">
                        <i class="fa-solid fa-certificate text-gold-600"></i>
                        <span>Ulasan Pembelian Terverifikasi</span>
                    </span>
                    <h1 class="font-display text-2xl font-bold text-neutral-900 dark:text-white">Bagikan Pengalaman Berbelanja</h1>
                    <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        Ulasan Anda membantu pelanggan lain dan mendukung pengrajin keluarga RHS Heritage mempertahankan standar mutu kriya.
                    </p>
                </div>
                <div class="text-right sm:border-l sm:border-neutral-200 dark:sm:border-neutral-700 sm:pl-6">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Nomor Pesanan</p>
                    <p class="font-mono font-bold text-primary-700 dark:text-gold-400 text-sm">#{{ $order->order_number }}</p>
                    <p class="text-[11px] text-neutral-400">{{ $order->created_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            <!-- Ringkasan Produk dalam Pesanan -->
            <div class="mt-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                <p class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 mb-2">Produk yang diterima:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($order->items as $item)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-canvas-sunken dark:bg-neutral-800 text-xs text-neutral-700 dark:text-neutral-300 border border-neutral-200 dark:border-neutral-700">
                            <i class="fa-solid fa-box text-neutral-400 text-[10px]"></i>
                            <span>{{ $item->product_name }}</span>
                            <span class="text-neutral-400">({{ $item->quantity }}x)</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 font-body text-xs space-y-1">
                <p class="font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>Terdapat kendala pada formulir ulasan:</span>
                </p>
                <ul class="list-disc list-inside space-y-0.5 pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulir Ulasan -->
        <form action="{{ route('customer.reviews.store', $order->order_number) }}" method="POST" class="bg-white dark:bg-[#15221d] rounded-2xl border border-neutral-200 dark:border-neutral-700 p-6 sm:p-8 shadow-1 space-y-6">
            @csrf

            <!-- Identitas Pelanggan (Otomatis & Terproteksi) -->
            <div>
                <label class="block font-body text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1.5">
                    Nama Pelanggan (Otomatis dari Akun)
                </label>
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-canvas-sunken dark:bg-neutral-800/80 border border-neutral-200 dark:border-neutral-700">
                    <div class="w-8 h-8 rounded-full bg-primary-700 text-gold-400 font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-body text-sm font-semibold text-neutral-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="font-body text-[11px] text-neutral-400">Nama akan disamarkan secara aman di beranda publik (contoh: {{ auth()->user()->name ? explode(' ', auth()->user()->name)[0] . ' ' . (isset(explode(' ', auth()->user()->name)[1]) ? substr(explode(' ', auth()->user()->name)[1], 0, 1) . '.' : '') : 'Pelanggan' }})</p>
                    </div>
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1 shrink-0">
                        <i class="fa-solid fa-shield-check"></i>
                        <span>Terverifikasi</span>
                    </span>
                </div>
            </div>

            <!-- Pemilihan Bintang Rating (1-5) -->
            <div>
                <label class="block font-body text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">
                    Tingkat Kepuasan Belanja <span class="text-danger">*</span>
                </label>
                
                <input type="hidden" name="rating" :value="rating">

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1 text-2xl sm:text-3xl">
                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                            <button 
                                type="button" 
                                @click="rating = star" 
                                @mouseenter="hoverRating = star"
                                class="transition-transform transform hover:scale-110 focus:outline-none p-1"
                                :title="'Beri ' + star + ' Bintang'"
                            >
                                <i :class="star <= rating ? 'fa-solid fa-star text-gold-500' : 'fa-regular fa-star text-neutral-300 dark:text-neutral-600'"></i>
                            </button>
                        </template>
                    </div>
                    <span class="font-body text-sm font-bold text-neutral-800 dark:text-neutral-200" x-text="ratingLabel"></span>
                </div>
            </div>

            <!-- Isi Ulasan -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="review" class="block font-body text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Ulasan Anda <span class="text-danger">*</span>
                    </label>
                    <span class="font-body text-xs text-neutral-400" :class="{ 'text-danger font-bold': reviewText.length > maxChars }">
                        <span x-text="reviewText.length"></span> / <span x-text="maxChars"></span> karakter
                    </span>
                </div>
                <textarea
                    id="review"
                    name="review"
                    rows="5"
                    x-model="reviewText"
                    maxlength="1000"
                    placeholder="Ceritakan pengalaman Anda mengenai kerapian jahitan, kenyamanan bahan kriya, ketepatan waktu pengiriman, dan pelayanan toko..."
                    class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-black/40 text-neutral-800 dark:text-neutral-100 placeholder-neutral-400 p-4 font-body text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    required
                ></textarea>
                <div class="mt-2 flex items-start gap-2 text-[11px] text-neutral-500 dark:text-neutral-400 leading-relaxed">
                    <i class="fa-solid fa-circle-info text-primary-600 mt-0.5 shrink-0"></i>
                    <span>
                        Minimal 10 karakter. Kami menerima apresiasi maupun kritik yang jujur dan membangun. Ulasan yang mengandung ujaran kebencian, kata kasar/SARA, tautan promosi/spam, dan nomor kontak pribadi akan ditolak secara otomatis demi keamanan bersama.
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between gap-3">
                <a 
                    href="{{ route('customer.orders.show', $order->order_number) }}" 
                    class="px-5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 font-body text-xs font-bold hover:bg-neutral-50 dark:hover:bg-neutral-800 transition"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    :disabled="reviewText.trim().length < 10 || reviewText.length > maxChars"
                    class="px-6 py-2.5 rounded-xl bg-primary-700 hover:bg-primary-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-body text-xs font-bold shadow-md transition flex items-center gap-2"
                >
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Ulasan</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.customer>
