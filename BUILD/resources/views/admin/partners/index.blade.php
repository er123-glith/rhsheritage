<x-layouts.admin>
    <x-slot:title>Pengajuan Grosir & Mitra</x-slot:title>

    <div class="space-y-6">
        <!-- Breadcrumb & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-body text-neutral-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-neutral-700 font-medium">Grosir & Mitra</span>
                </nav>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-primary-700">Pengajuan Grosir & Mitra</h1>
                <p class="font-body text-sm text-neutral-500 mt-1">
                    Kelola permohonan kemitraan harga grosir dan toko mitra ritel dari instansi, pondok pesantren, sekolah, dan reseller.
                </p>
            </div>
            @if($pendingCount > 0)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gold-100/90 border border-gold-300 text-gold-900 font-body text-xs font-bold shadow-xs shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500 animate-ping"></span>
                    <span>{{ $pendingCount }} Pengajuan Perlu Ditindaklanjuti</span>
                </div>
            @endif
        </div>

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total -->
            <a href="{{ route('admin.partners.index') }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ !request('status') && !request('type') ? 'bg-primary-50/80 border-primary-500 shadow-sm ring-1 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-primary-300 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Total Pengajuan</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-primary-600"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-primary-700 mt-1">{{ $totalCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Semua permohonan masuk</p>
            </a>

            <!-- Pending Follow-up -->
            <a href="{{ route('admin.partners.index', ['status' => 'pending']) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('status') === 'pending' ? 'bg-warning-bg/90 border-warning shadow-sm ring-1 ring-warning/30' : 'bg-white border-neutral-200 hover:border-warning/50 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-warning transition">Belum Follow-Up</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-warning animate-pulse"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-warning mt-1">{{ $pendingCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Menunggu respon owner</p>
            </a>

            <!-- Wholesale Type -->
            <a href="{{ route('admin.partners.index', ['type' => 'grosir']) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('type') === 'grosir' ? 'bg-primary-50/80 border-primary-500 shadow-sm ring-1 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-primary-300 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Harga Grosir</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-primary-700 mt-1">{{ $wholesaleCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Pesantren, sekolah, yayasan</p>
            </a>

            <!-- Partner Store Type -->
            <a href="{{ route('admin.partners.index', ['type' => 'mitra']) }}" 
               class="p-4 sm:p-5 rounded-xl border transition group {{ request('type') === 'mitra' ? 'bg-primary-50/80 border-primary-500 shadow-sm ring-1 ring-primary-500/20' : 'bg-white border-neutral-200 hover:border-primary-300 shadow-1' }}">
                <div class="flex items-center justify-between">
                    <p class="font-body text-xs uppercase tracking-wider text-neutral-500 font-semibold group-hover:text-primary-700 transition">Toko Mitra</p>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                <p class="font-body text-2xl sm:text-3xl font-bold text-primary-700 mt-1">{{ $partnerCount }}</p>
                <p class="font-body text-xs text-neutral-400 mt-0.5">Toko ritel busana muslim</p>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('admin.partners.index') }}" class="bg-white rounded-xl border border-neutral-200 shadow-1 p-4 flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama pemohon, toko/instansi, no. WhatsApp, kota..."
                    class="w-full h-11 pl-10 pr-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200"
                />
                <svg class="w-5 h-5 text-neutral-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="type" class="h-11 px-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 bg-white text-neutral-700">
                <option value="">Semua Jenis Kemitraan</option>
                <option value="grosir" @selected(request('type') === 'grosir')>Harga Grosir (Pesantren / Sekolah)</option>
                <option value="mitra" @selected(request('type') === 'mitra')>Toko Mitra (Ritel Busana)</option>
            </select>

            <select name="status" class="h-11 px-4 rounded-lg font-body text-sm border border-neutral-200 focus:outline-none focus:border-primary-700 focus:ring-2 focus:ring-primary-200 bg-white text-neutral-700">
                <option value="">Semua Status Follow-Up</option>
                <option value="pending" @selected(request('status') === 'pending')>Belum Ditindaklanjuti</option>
                <option value="followed_up" @selected(request('status') === 'followed_up')>Sudah Ditindaklanjuti</option>
            </select>

            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" class="h-11 px-5">Filter</x-button>
                @if(request()->hasAny(['q', 'type', 'status']))
                    <a href="{{ route('admin.partners.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-lg font-body text-sm border border-neutral-300 text-neutral-600 hover:bg-neutral-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Applications List / Cards -->
        <div class="space-y-4">
            @forelse($applications as $app)
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $app->phone);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                    $typeLabel = $app->type === \App\Enums\ApplicationType::Grosir ? 'Harga Grosir' : 'Toko Mitra';
                    $waText = rawurlencode("Halo Bapak/Ibu {$app->applicant_name}, kami dari RHS HERITAGE ingin menindaklanjuti formulir pengajuan {$typeLabel} yang Anda kirimkan melalui website kami.");
                    $waLink = "https://wa.me/{$cleanPhone}?text={$waText}";
                @endphp

                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 hover:border-primary-300 transition">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 pb-4 border-b border-neutral-100">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <!-- Type Badge -->
                                @if($app->type === \App\Enums\ApplicationType::Grosir)
                                    <span class="px-2.5 py-1 rounded-md bg-primary-50 border border-primary-200 text-primary-700 font-body text-xs font-bold tracking-wide">
                                        Harga Grosir
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md bg-gold-100 border border-gold-300 text-gold-900 font-body text-xs font-bold tracking-wide">
                                        Toko Mitra
                                    </span>
                                @endif

                                <!-- Follow-up Status Badge -->
                                @if($app->is_followed_up)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-success border border-green-200">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Sudah Ditindaklanjuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-warning-bg text-warning border border-warning/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-warning animate-pulse"></span>
                                        Belum Ditindaklanjuti
                                    </span>
                                @endif

                                <span class="text-xs text-neutral-400 font-body">
                                    • Masuk pada {{ $app->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <h2 class="font-display text-lg font-bold text-neutral-900 mt-1.5">
                                {{ $app->applicant_name }}
                                @if($app->institution_name)
                                    <span class="text-sm font-body font-normal text-neutral-500">({{ $app->institution_name }})</span>
                                @endif
                            </h2>
                            <p class="font-body text-xs text-neutral-500 mt-0.5">
                                Wilayah / Asal: <strong class="text-neutral-700">{{ $app->region }}</strong>
                            </p>
                        </div>

                        <!-- Top Quick Actions (WhatsApp & Follow-up Toggle & Delete) -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <a 
                                href="{{ $waLink }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="px-3.5 py-2 rounded-lg bg-[#25D366] hover:bg-[#1EBE5D] text-white font-body text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Hubungi via WA</span>
                            </a>

                            <form method="POST" action="{{ route('admin.partners.toggle-followup', $app) }}">
                                @csrf
                                @method('PATCH')
                                <button 
                                    type="submit" 
                                    class="px-3.5 py-2 rounded-lg border font-body text-xs font-semibold transition {{ $app->is_followed_up ? 'border-neutral-300 text-neutral-600 hover:bg-neutral-50' : 'border-success text-success hover:bg-green-50' }}"
                                >
                                    {{ $app->is_followed_up ? 'Tandai Belum Follow-Up' : 'Tandai Selesai Follow-Up' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.partners.destroy', $app) }}" data-confirm="Hapus pengajuan kemitraan dari {{ $app->applicant_name }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-neutral-400 hover:text-danger hover:bg-danger-bg transition" title="Hapus pengajuan">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 text-xs font-body">
                        <!-- Contact info -->
                        <div class="space-y-1.5">
                            <p class="text-neutral-400 uppercase font-semibold">Kontak Pemohon</p>
                            <p class="text-neutral-800 font-mono flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $app->phone }}</span>
                            </p>
                            @if($app->email)
                                <p class="text-neutral-800 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <a href="mailto:{{ $app->email }}" class="underline hover:text-primary-700">{{ $app->email }}</a>
                                </p>
                            @endif
                        </div>

                        <!-- Product Interest & Qty -->
                        <div class="space-y-1.5">
                            <p class="text-neutral-400 uppercase font-semibold">Minat Produk & Estimasi Volume</p>
                            <p class="text-neutral-900 font-semibold flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span>{{ $app->product_interest ?: 'Seluruh katalog RHS' }}</span>
                            </p>
                            @if($app->estimated_quantity)
                                <p class="text-neutral-600">Estimasi: <strong class="text-primary-700 font-bold">{{ $app->estimated_quantity }} kodi / pcs</strong></p>
                            @endif
                        </div>

                        <!-- Message Notes -->
                        <div class="space-y-1.5 md:col-span-1">
                            <p class="text-neutral-400 uppercase font-semibold">Catatan / Pesan Pemohon</p>
                            @if($app->message)
                                <p class="text-neutral-700 italic bg-canvas-sunken p-2.5 rounded-lg border border-neutral-200/60 leading-relaxed">
                                    "{{ $app->message }}"
                                </p>
                            @else
                                <p class="text-neutral-400 italic">Tidak ada catatan tambahan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-16 text-center">
                    <div class="max-w-sm mx-auto">
                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mx-auto mb-4 text-neutral-400">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <p class="font-display font-bold text-neutral-800 text-base">Belum Ada Pengajuan Kemitraan</p>
                        <p class="font-body text-xs text-neutral-500 mt-1">Formulir pengajuan dari halaman Grosir & Toko Mitra publik akan otomatis masuk dan tampil di sini.</p>
                        @if(request()->hasAny(['q', 'type', 'status']))
                            <a href="{{ route('admin.partners.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-700 text-white font-body text-xs font-semibold hover:bg-primary-800 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Tampilkan Semua Pengajuan</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="bg-white rounded-xl border border-neutral-200 p-4 shadow-1">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
