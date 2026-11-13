@props([
    'order',
    'context' => 'customer', // 'admin', 'customer', 'public'
])

@php
    $progress = $order->getTimelineProgress();
    $steps = $progress['steps'];
    $cancellation = $progress['cancellation'];
    $isCancelled = $progress['is_cancelled'];
    $activeReturn = $order->activeReturn();
    $rawLogs = $order->relationLoaded('statusLogs') ? $order->statusLogs : $order->statusLogs()->get();

    foreach ($rawLogs as $l) {
        if (! $l->relationLoaded('order')) {
            $l->setRelation('order', $order);
        }
    }

    // Urutkan kronologis dari paling awal hingga terbaru.
    // Menjamin urutan baku alur bisnis: Menunggu Konfirmasi -> Diproses -> Dikirim -> Selesai
    $logs = $rawLogs->sortBy(function ($l) use ($order) {
        $to = $l->to_status instanceof \App\Enums\OrderStatus ? $l->to_status->value : (string) $l->to_status;
        $orderPriority = match($to) {
            \App\Enums\OrderStatus::MenungguKonfirmasi->value => 1,
            \App\Enums\OrderStatus::Diproses->value           => 2,
            \App\Enums\OrderStatus::Dikirim->value            => 3,
            \App\Enums\OrderStatus::Selesai->value            => 4,
            \App\Enums\OrderStatus::Dibatalkan->value         => 5,
            default                                           => 99,
        };

        // Jika log menunggu_konfirmasi tercatat lebih baru dari order created_at, gunakan created_at order
        $effectiveTime = ($to === \App\Enums\OrderStatus::MenungguKonfirmasi->value && $l->created_at && $order->created_at && $l->created_at > $order->created_at)
            ? $order->created_at
            : ($l->created_at ?? $order->created_at);

        $timestamp = $effectiveTime ? $effectiveTime->timestamp : 0;
        return sprintf('%012d-%02d-%010d', $timestamp, $orderPriority, $l->id ?? 0);
    })->values();
@endphp

<div class="space-y-6">
    <!-- Card Utama: Progress Stepper 4 Tahapan RHS Heritage -->
    <div class="bg-white dark:bg-[#15221d] rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-1 p-6 sm:p-7 transition-colors">
        <!-- Header Stepper -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-neutral-100 dark:border-neutral-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary-700 dark:bg-gold-400"></span>
                    <h2 class="font-display text-base sm:text-lg font-bold text-neutral-900 dark:text-white">
                        Alur & Progres Pesanan
                    </h2>
                </div>
                <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    Tahapan transaksi terstruktur: Menunggu Konfirmasi &rarr; Diproses &rarr; Dikirim &rarr; Selesai
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-status-badge :status="$order->status" class="text-xs px-3 py-1 font-bold" />
                @if($isCancelled)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-danger-bg text-danger border border-danger/30">
                        <i class="fa-solid fa-ban text-[11px]"></i>
                        <span>Dibatalkan</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Warning jika terdeteksi inkonsistensi tahapan yang dilewati -->
        @if($progress['has_inconsistency'])
            <div class="mt-4 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700 text-amber-900 dark:text-amber-200 text-xs flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-0.5 text-sm shrink-0"></i>
                <div class="leading-relaxed">
                    <span class="font-bold block">Pemberitahuan Inkonsistensi Riwayat:</span>
                    <span>Terdapat tahapan alur yang tidak tercatat riwayat statusnya dalam basis data. Sistem menampilkan status berdasarkan bukti kejadian aktual tanpa membuat data palsu.</span>
                </div>
            </div>
        @endif

        <!-- Progress Timeline Bar (Responsive: Vertical on Mobile, Horizontal on Desktop) -->
        <div class="mt-6">
            <!-- Desktop & Tablet Stepper (md:flex) -->
            <div class="hidden md:flex items-start justify-between relative">
                @foreach($steps as $index => $step)
                    @php
                        $isLast = ($index === count($steps) - 1);
                        $stepState = $step['state'];
                    @endphp

                    <div class="flex-1 relative flex flex-col items-center text-center px-2 group">
                        <!-- Connector Line to Next Step -->
                        @if(! $isLast)
                            @php
                                $nextStep = $steps[$index + 1];
                                $lineCompleted = ($step['is_completed'] && ($nextStep['is_completed'] || $nextStep['is_current']));
                                $lineCancelled = ($isCancelled && ! $nextStep['is_completed']);
                            @endphp
                            <div class="absolute top-5 left-1/2 w-full h-1 -translate-y-1/2 z-0 {{ $lineCompleted ? 'bg-emerald-500' : ($lineCancelled ? 'bg-neutral-200 dark:bg-neutral-800' : 'bg-neutral-200 dark:bg-neutral-700') }}">
                                @if($step['is_current'] && ! $isCancelled)
                                    <div class="h-full bg-gradient-to-r from-gold-500 to-neutral-200 dark:to-neutral-700"></div>
                                @endif
                            </div>
                        @endif

                        <!-- Step Circle Icon -->
                        <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-200 shadow-xs
                            @if($stepState === 'completed')
                                bg-emerald-600 text-white ring-4 ring-emerald-100 dark:ring-emerald-950/60
                            @elseif($stepState === 'current')
                                bg-gold-500 text-neutral-900 ring-4 ring-gold-200 dark:ring-gold-950/70 animate-pulse
                            @elseif($stepState === 'cancelled')
                                bg-neutral-200 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-600 border border-neutral-300 dark:border-neutral-700
                            @elseif($stepState === 'skipped')
                                bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border-2 border-dashed border-amber-400
                            @else
                                bg-canvas-sunken dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 border border-neutral-300 dark:border-neutral-700
                            @endif
                        ">
                            @if($stepState === 'completed')
                                <i class="fa-solid fa-check text-xs"></i>
                            @elseif($stepState === 'cancelled')
                                <i class="fa-solid fa-xmark text-xs"></i>
                            @elseif($stepState === 'skipped')
                                <i class="fa-solid fa-question text-xs"></i>
                            @else
                                <i class="{{ $step['icon'] }} text-xs"></i>
                            @endif
                        </div>

                        <!-- Step Labels -->
                        <div class="mt-3 space-y-1 max-w-[140px]">
                            <!-- Step Badge / Status Indicator -->
                            <div>
                                @if($stepState === 'completed')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300">
                                        Selesai
                                    </span>
                                @elseif($stepState === 'current')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gold-100 dark:bg-gold-950 text-gold-900 dark:text-gold-300 ring-1 ring-gold-400/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gold-600 dark:bg-gold-400 animate-ping"></span>
                                        <span>Saat Ini</span>
                                    </span>
                                @elseif($stepState === 'cancelled')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 line-through">
                                        Dibatalkan
                                    </span>
                                @elseif($stepState === 'skipped')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">
                                        Dilewati
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500">
                                        Belum Dilalui
                                    </span>
                                @endif
                            </div>

                            <p class="font-body text-xs font-bold leading-tight {{ $stepState === 'current' ? 'text-primary-700 dark:text-gold-400' : ($stepState === 'completed' ? 'text-neutral-900 dark:text-white' : 'text-neutral-400 dark:text-neutral-500') }}">
                                {{ $step['label'] }}
                            </p>

                            <!-- Actual Timestamp (ONLY if actually recorded) -->
                            @if($step['formatted_time'])
                                <p class="text-[11px] font-mono text-neutral-500 dark:text-neutral-400 font-medium">
                                    {{ $step['formatted_time'] }}
                                </p>
                            @else
                                <p class="text-[10px] text-neutral-300 dark:text-neutral-600 italic">
                                    -
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile Stepper (Stacked Vertical from Top to Bottom) -->
            <div class="md:hidden space-y-4 relative pl-4 border-l-2 border-neutral-200 dark:border-neutral-700 ml-3">
                @foreach($steps as $step)
                    @php
                        $stepState = $step['state'];
                    @endphp
                    <div class="relative pl-5">
                        <!-- Bullet / Node on Vertical Line -->
                        <span class="absolute -left-[23px] top-1 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold
                            @if($stepState === 'completed')
                                bg-emerald-600 text-white ring-4 ring-white dark:ring-[#15221d]
                            @elseif($stepState === 'current')
                                bg-gold-500 text-neutral-900 ring-4 ring-gold-200 dark:ring-gold-950 animate-pulse
                            @elseif($stepState === 'cancelled')
                                bg-neutral-200 dark:bg-neutral-800 text-neutral-400 border border-neutral-300 dark:border-neutral-700 ring-4 ring-white dark:ring-[#15221d]
                            @elseif($stepState === 'skipped')
                                bg-amber-100 text-amber-700 border border-amber-400 ring-4 ring-white dark:ring-[#15221d]
                            @else
                                bg-neutral-100 dark:bg-neutral-800 text-neutral-400 border border-neutral-300 dark:border-neutral-700 ring-4 ring-white dark:ring-[#15221d]
                            @endif
                        ">
                            @if($stepState === 'completed')
                                <i class="fa-solid fa-check"></i>
                            @elseif($stepState === 'cancelled')
                                <i class="fa-solid fa-xmark"></i>
                            @elseif($stepState === 'skipped')
                                <i class="fa-solid fa-question"></i>
                            @else
                                <i class="{{ $step['icon'] }}"></i>
                            @endif
                        </span>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-body text-xs font-bold {{ $stepState === 'current' ? 'text-primary-700 dark:text-gold-400' : ($stepState === 'completed' ? 'text-neutral-900 dark:text-white' : 'text-neutral-400 dark:text-neutral-500') }}">
                                    {{ $step['label'] }}
                                </span>

                                @if($stepState === 'completed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">
                                        Selesai
                                    </span>
                                @elseif($stepState === 'current')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gold-100 dark:bg-gold-950 text-gold-900 dark:text-gold-300">
                                        Sedang Berlangsung
                                    </span>
                                @elseif($stepState === 'cancelled')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500 line-through">
                                        Dibatalkan
                                    </span>
                                @elseif($stepState === 'skipped')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">
                                        Dilewati
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-neutral-100 dark:bg-neutral-800 text-neutral-400 dark:text-neutral-500">
                                        Belum Dilalui
                                    </span>
                                @endif
                            </div>

                            @if($step['formatted_time'])
                                <p class="text-[11px] font-mono text-neutral-500 dark:text-neutral-400">
                                    {{ $step['formatted_time'] }}
                                </p>
                            @endif

                            <p class="text-[11px] text-neutral-500 dark:text-neutral-400 leading-snug">
                                {{ $step['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cabang Khusus Pembatalan (Jika Pesanan Dibatalkan) -->
        @if($isCancelled && $cancellation)
            <div class="mt-6 p-4 rounded-xl bg-danger-bg/80 dark:bg-danger-bg/20 border border-danger/30 dark:border-danger/40 text-danger flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-danger/10 text-danger flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    <i class="fa-solid fa-circle-xmark text-base"></i>
                </div>
                <div class="space-y-1 font-body text-xs">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-sm uppercase tracking-wider">Cabang Status: Pesanan Dibatalkan</span>
                        @if($cancellation['formatted_time'])
                            <span class="text-neutral-500 dark:text-neutral-400 font-mono">({{ $cancellation['formatted_time'] }})</span>
                        @endif
                    </div>
                    <p class="text-neutral-700 dark:text-neutral-300">
                        <strong>Alasan:</strong> {{ $cancellation['reason'] }}
                    </p>
                    <p class="text-[11px] text-neutral-500 dark:text-neutral-400 italic">
                        Pesanan ini dihentikan dan stok barang telah dikembalikan. Alur pesanan tidak dilanjutkan ke tahap Pengiriman maupun Selesai.
                    </p>
                </div>
            </div>
        @endif

        <!-- Indikator Khusus Retur Aktif (Terpisah dari 4 Alur Utama Pesanan) -->
        @if($activeReturn)
            <div class="mt-6 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/80 font-body flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-rotate-left text-sm"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-xs uppercase tracking-wider text-amber-900 dark:text-amber-200">
                                Pengajuan Retur Aktif
                            </span>
                            <span class="font-mono text-xs font-bold text-neutral-800 dark:text-white">
                                #{{ $activeReturn->return_number }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $activeReturn->status->badgeClasses() }}">
                                {{ $activeReturn->status->label() }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-white dark:bg-neutral-800 text-neutral-700 dark:text-neutral-300 text-[10px] font-semibold border border-neutral-200 dark:border-neutral-700">
                                {{ $activeReturn->solutionLabel() }}
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                            Proses klaim retur ditangani secara mandiri pada riwayat terpisah (Menunggu Review &rarr; Verifikasi &rarr; Pengembalian Barang &rarr; {{ $activeReturn->solution === 'refund' ? 'Refund Dana' : 'Replacement Barang' }} &rarr; Penyelesaian).
                        </p>
                    </div>
                </div>

                <div class="shrink-0 pl-12 sm:pl-0">
                    @if($context === 'admin')
                        <a 
                            href="{{ route('admin.returns.show', $activeReturn->return_number) }}" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white text-xs font-bold transition shadow-xs"
                        >
                            <span>Kelola Riwayat Retur</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @elseif($context === 'customer')
                        <a 
                            href="{{ route('customer.returns.show', $activeReturn->return_number) }}" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white text-xs font-bold transition shadow-xs"
                        >
                            <span>Pantau Perkembangan Retur</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <span class="text-xs text-neutral-500 italic">Hubungi customer care untuk detail</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Card Riwayat Aktivitas & Perubahan Status (Kronologis Aktual: Terawal -> Terbaru) -->
    <div class="bg-white dark:bg-[#15221d] rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-1 p-6 sm:p-7 transition-colors">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100 dark:border-neutral-800">
            <div>
                <h3 class="font-display text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary-700 dark:text-gold-400 text-sm"></i>
                    <span>Riwayat Aktivitas & Log Status</span>
                </h3>
                <p class="font-body text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Urutan kronologis kejadian riil dari awal pemesanan hingga pembaruan terkini
                </p>
            </div>
            <span class="font-body text-xs font-semibold px-2.5 py-1 rounded-full bg-canvas-sunken dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300">
                {{ $logs->count() }} Peristiwa
            </span>
        </div>

        <div class="mt-5">
            @if($logs->isEmpty())
                <p class="font-body text-xs text-neutral-400 dark:text-neutral-500 italic py-4 text-center">
                    Belum ada riwayat aktivitas yang tercatat untuk pesanan ini.
                </p>
            @else
                <div class="relative pl-6 sm:pl-7 border-l-2 border-primary-200 dark:border-primary-800/80 space-y-6">
                    @foreach($logs as $index => $log)
                        @php
                            $toStatus = $log->to_status instanceof \App\Enums\OrderStatus
                                ? $log->to_status
                                : (is_string($log->to_status) ? \App\Enums\OrderStatus::tryFrom($log->to_status) : null);
                            $isLastLog = ($index === $logs->count() - 1);
                        @endphp
                        <div class="relative group">
                            <!-- Bullet Marker on Connector Line -->
                            <span class="absolute -left-[31px] sm:-left-[35px] top-1.5 w-4 h-4 rounded-full border-2 border-white dark:border-[#15221d] ring-4 ring-primary-100 dark:ring-primary-950/80
                                {{ $isLastLog ? 'bg-gold-500' : 'bg-primary-700 dark:bg-primary-500' }}
                            "></span>

                            <div class="space-y-1.5 font-body">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-xs uppercase tracking-wider text-neutral-900 dark:text-white">
                                            {{ \App\Enums\OrderStatus::labelFor($log->to_status) }}
                                        </span>

                                        @if($log->from_status)
                                            <span class="text-[11px] text-neutral-400 dark:text-neutral-500">
                                                (dari: {{ \App\Enums\OrderStatus::labelFor($log->from_status) }})
                                            </span>
                                        @endif

                                        @if($isLastLog)
                                            <span class="px-2 py-0.2 rounded text-[10px] font-bold bg-primary-50 dark:bg-primary-950 text-primary-700 dark:text-gold-400 border border-primary-200 dark:border-primary-800">
                                                Status Terkini
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Tanggal dan Jam Perubahan Faktural -->
                                    <div class="text-xs text-neutral-400 dark:text-neutral-500 font-mono">
                                        <i class="fa-regular fa-clock text-[10px] mr-1"></i>
                                        <span>{{ $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i') : '-' }} WIB</span>
                                    </div>
                                </div>

                                <!-- Pihak yang melakukan perubahan -->
                                <p class="text-[11px] text-neutral-500 dark:text-neutral-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-shield text-[10px] text-neutral-400"></i>
                                    <span>Pihak: <strong class="text-neutral-700 dark:text-neutral-300">{{ $log->actor_label }}</strong></span>
                                </p>

                                <!-- Keterangan Perubahan / Catatan -->
                                @if($log->note)
                                    <div class="mt-1 p-3 rounded-xl bg-canvas-sunken dark:bg-neutral-800/80 border border-neutral-200/70 dark:border-neutral-700/60 text-xs text-neutral-700 dark:text-neutral-300 leading-relaxed">
                                        {{ $log->note }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
