<x-layouts.admin>
    <x-slot:title>Manajemen Ulasan Pelanggan</x-slot:title>

    <div class="space-y-6" x-data="{
        rejectModalOpen: false,
        rejectActionUrl: '',
        rejectOrderNumber: '',
        rejectionReason: '',
        openRejectModal(url, orderNum) {
            this.rejectActionUrl = url;
            this.rejectOrderNumber = orderNum;
            this.rejectionReason = '';
            this.rejectModalOpen = true;
        }
    }">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-neutral-700 font-medium">Manajemen Ulasan</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Ulasan & Rating Toko</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Moderasi ulasan pengalaman belanja dari pelanggan dengan pesanan terverifikasi. Pastikan transparansi tanpa manipulasi kritik pelanggan yang sah.
                </p>
            </div>
            @if($pendingCount > 0)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-100 border border-amber-300 text-amber-900 font-body text-xs font-bold shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>{{ $pendingCount }} Ulasan Perlu Ditinjau</span>
                </div>
            @endif
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-sm border-b border-neutral-200">
            <a href="{{ route('admin.reviews.index', array_filter(['q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ !request('status') ? 'border-primary-700 text-primary-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Semua Ulasan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-neutral-100 text-neutral-600">{{ $allCount }}</span>
            </a>

            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'pending_review', 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'pending_review' ? 'border-amber-500 text-amber-800 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-amber-700 hover:border-amber-300' }}">
                <span>Menunggu Moderasi</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">{{ $pendingCount }}</span>
            </a>

            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'published', 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'published' ? 'border-emerald-600 text-emerald-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-emerald-700 hover:border-emerald-300' }}">
                <span>Dipublikasikan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">{{ $publishedCount }}</span>
            </a>

            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'rejected', 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'rejected' ? 'border-rose-600 text-rose-700 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-rose-700 hover:border-rose-300' }}">
                <span>Ditolak</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">{{ $rejectedCount }}</span>
            </a>

            <a href="{{ route('admin.reviews.index', array_filter(['status' => 'hidden', 'q' => request('q')])) }}" 
               class="px-4 py-2.5 rounded-t-lg font-body font-semibold transition border-b-2 flex items-center gap-2 shrink-0 {{ request('status') === 'hidden' ? 'border-neutral-700 text-neutral-800 bg-white shadow-xs' : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300' }}">
                <span>Disembunyikan</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-neutral-200 text-neutral-800">{{ $hiddenCount }}</span>
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 flex flex-col sm:flex-row gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari berdasarkan nama customer, nomor pesanan, atau isi ulasan..."
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <i class="fa-solid fa-magnifying-glass text-neutral-400 absolute left-3.5 top-3.5 text-sm"></i>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="h-11 px-5 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-body text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Cari</span>
                </button>
                @if(request()->hasAny(['q', 'status']))
                    <a href="{{ route('admin.reviews.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Reviews Table -->
        <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
            @if($reviews->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-primary-50 text-primary-700 mx-auto flex items-center justify-center text-xl">
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-base font-bold text-neutral-800">Tidak Ada Ulasan Ditemukan</h3>
                        <p class="font-body text-xs text-neutral-500 mt-1 max-w-sm mx-auto">
                            @if(request()->hasAny(['q', 'status']))
                                Tidak ada ulasan yang sesuai dengan filter atau kata kunci pencarian.
                            @else
                                Belum ada pelanggan yang mengirimkan ulasan pengalaman berbelanja.
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm font-body">
                        <thead class="bg-canvas-sunken border-b border-neutral-200 text-xs font-semibold uppercase tracking-wider text-neutral-500">
                            <tr>
                                <th class="px-6 py-3.5">Pelanggan & Pesanan</th>
                                <th class="px-4 py-3.5">Rating</th>
                                <th class="px-6 py-3.5">Isi Ulasan Pelanggan</th>
                                <th class="px-4 py-3.5">Status & Catatan</th>
                                <th class="px-6 py-3.5 text-right">Tindakan Moderasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach($reviews as $rev)
                                <tr class="hover:bg-canvas-sunken/40 transition">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-bold text-neutral-900">{{ $rev->user?->name ?? 'Pelanggan' }}</div>
                                        <div class="text-xs text-neutral-400">{{ $rev->user?->email }}</div>
                                        <div class="mt-2 flex items-center gap-1.5">
                                            <a href="{{ route('admin.orders.show', $rev->order_id) }}" class="font-mono text-xs font-semibold text-primary-700 hover:underline">
                                                #{{ $rev->order?->order_number }}
                                            </a>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                                                Selesai
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top whitespace-nowrap">
                                        <div class="flex items-center gap-1 text-gold-500 text-sm">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $rev->rating ? 'fa-solid fa-star' : 'fa-regular fa-star text-neutral-300' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-bold text-neutral-700 mt-0.5 block">{{ $rev->rating }} / 5.0</span>
                                    </td>
                                    <td class="px-6 py-4 align-top max-w-md">
                                        <p class="text-xs text-neutral-800 leading-relaxed font-body">
                                            "{{ $rev->review }}"
                                        </p>
                                        <div class="mt-2 text-[11px] text-neutral-400 flex items-center gap-2">
                                            <span>Dikirim: {{ $rev->created_at->translatedFormat('d M Y, H:i') }}</span>
                                            @if($rev->published_at)
                                                <span>• Tayang: {{ $rev->published_at->translatedFormat('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold border {{ $rev->moderation_status_badge_class }}">
                                            {{ $rev->moderation_status_label }}
                                        </span>
                                        @if($rev->moderation_reason)
                                            <p class="text-[11px] text-neutral-500 mt-1 max-w-xs leading-normal">
                                                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-[10px]"></i>
                                                <span>{{ $rev->moderation_reason }}</span>
                                            </p>
                                        @endif
                                        @if($rev->moderator)
                                            <p class="text-[10px] text-neutral-400 mt-1">
                                                Oleh: {{ $rev->moderator->name }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            @if($rev->moderation_status !== \App\Models\Review::STATUS_PUBLISHED)
                                                <form method="POST" action="{{ route('admin.reviews.approve', $rev->id) }}" class="inline" data-confirm="Apakah Anda yakin ingin mempublikasikan ulasan ini ke beranda toko?">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-body text-xs font-bold transition shadow-xs flex items-center gap-1">
                                                        <i class="fa-solid fa-check text-[10px]"></i>
                                                        <span>Setujui</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($rev->moderation_status !== \App\Models\Review::STATUS_REJECTED)
                                                <button 
                                                    type="button" 
                                                    @click="openRejectModal('{{ route('admin.reviews.reject', $rev->id) }}', '{{ $rev->order?->order_number }}')"
                                                    class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-body text-xs font-bold transition flex items-center gap-1"
                                                >
                                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                                    <span>Tolak</span>
                                                </button>
                                            @endif

                                            @if($rev->moderation_status === \App\Models\Review::STATUS_PUBLISHED)
                                                <form method="POST" action="{{ route('admin.reviews.hide', $rev->id) }}" class="inline" data-confirm="Sembunyikan ulasan ini dari tampilan beranda publik?">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-body text-xs font-medium transition flex items-center gap-1" title="Sembunyikan ulasan dari publik">
                                                        <i class="fa-solid fa-eye-slash text-[10px]"></i>
                                                        <span>Sembunyikan</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reviews->hasPages())
                    <div class="px-6 py-4 border-t border-neutral-200">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Modal Tolak Ulasan -->
        <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="rejectModalOpen" @click="rejectModalOpen = false" class="fixed inset-0 bg-neutral-900/60 backdrop-blur-xs transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 space-y-4">
                    <div class="flex items-center gap-3 text-rose-600">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="font-display text-lg font-bold text-neutral-900" id="modal-title">Tolak Ulasan Pelanggan</h3>
                            <p class="text-xs text-neutral-500">Pesanan #<span x-text="rejectOrderNumber"></span></p>
                        </div>
                    </div>

                    <form :action="rejectActionUrl" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wider text-neutral-600 mb-1">
                                Alasan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                id="rejection_reason" 
                                name="rejection_reason" 
                                x-model="rejectionReason"
                                rows="3" 
                                required
                                placeholder="Contoh: Mengandung promosi eksternal / bahasa kasar / informasi pribadi sensitif..."
                                class="w-full rounded-xl border border-neutral-200 text-xs p-3 focus:ring-2 focus:ring-rose-500 focus:border-transparent"
                            ></textarea>
                            <p class="text-[11px] text-neutral-400 mt-1">Catatan: Kritik kualitas produk atau komplain yang sopan tidak boleh ditolak hanya karena bernada negatif.</p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-neutral-100">
                            <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-lg border border-neutral-200 text-xs font-semibold text-neutral-600 hover:bg-neutral-50">
                                Batal
                            </button>
                            <button type="submit" :disabled="!rejectionReason.trim()" class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-xs font-bold shadow-xs">
                                Konfirmasi Tolak Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
