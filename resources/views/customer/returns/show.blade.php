<x-layouts.customer>
    <x-slot:title>Detail Retur {{ $return->return_number }} — RHS HERITAGE</x-slot:title>

    <div class="space-y-6">
        <!-- Header & Breadcrumbs -->
        <nav class="flex items-center gap-2 font-body text-xs text-neutral-400">
            <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-700 transition">Dashboard</a>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('customer.returns.index') }}" class="hover:text-primary-700 transition">Retur Saya</a>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-neutral-700 font-medium font-mono">{{ $return->return_number }}</span>
        </nav>

        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-1 flex flex-col md:flex-row md:items-center justify-between gap-4 font-body">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="font-display text-2xl font-bold text-primary-700">{{ $return->return_number }}</h1>
                    <span class="px-3 py-1 rounded text-xs font-bold border {{ $return->status->badgeClasses() }}">
                        {{ $return->status->label() }}
                    </span>
                    <span class="px-2.5 py-1 rounded bg-neutral-100 text-neutral-800 text-xs font-semibold">
                        {{ $return->solutionLabel() }}
                    </span>
                </div>
                <p class="text-xs text-neutral-500 mt-1">
                    Diajukan pada {{ $return->created_at->translatedFormat('l, d F Y - H:i') }} WIB • 
                    Pesanan: <a href="{{ route('customer.orders.show', $return->order->order_number) }}" class="font-mono font-bold text-primary-700 hover:underline">#{{ $return->order->order_number }}</a>
                </p>
            </div>

            <!-- Manual Refresh Button -->
            <div>
                <button 
                    onclick="window.location.reload()" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-primary-300 bg-primary-50 text-primary-800 text-xs font-semibold hover:bg-primary-100 transition shadow-xs"
                >
                    <svg class="w-4 h-4 text-primary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Muat Ulang Status</span>
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-body text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 font-body text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Status Alerts & Customer Action Banners -->
        @if($return->status === \App\Enums\ReturnStatus::Ditolak)
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 font-body text-xs space-y-1">
                <p class="font-bold">Pengajuan Retur Ditolak:</p>
                <p class="text-rose-800">{{ $return->rejection_reason }}</p>
            </div>
        @elseif($return->status === \App\Enums\ReturnStatus::PerluInformasi)
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 font-body text-xs space-y-1">
                <p class="font-bold">Catatan dari Tim RHS Heritage:</p>
                <p class="text-blue-800">{{ $return->admin_notes }}</p>
                <p class="pt-2 text-[11px] text-blue-700">Silakan hubungi customer support via WhatsApp untuk melengkapi informasi yang diminta.</p>
            </div>
        @elseif($return->status === \App\Enums\ReturnStatus::MenungguBarangBalik)
            <!-- Form Upload Resi Pengembalian Barang J&T -->
            <div class="p-6 rounded-xl bg-purple-50 border border-purple-200 font-body space-y-4">
                <div>
                    <h3 class="font-display text-base font-bold text-purple-950">Instruksi Pengiriman Balik Barang</h3>
                    <p class="text-xs text-purple-800 mt-1 leading-relaxed">
                        Pengajuan Anda telah disetujui. Silakan kemas produk dengan aman dan kirimkan melalui ekspedisi <strong>J&T Express</strong> ke alamat gudang kami:
                    </p>
                    <div class="mt-2 p-3 bg-white rounded-lg border border-purple-200 text-xs text-neutral-800 font-medium">
                        <strong>Gudang RHS HERITAGE</strong><br>
                        Jl. Merpati No. 12, Pringsewu, Lampung 35373<br>
                        UP: Tim Penanganan Retur (0812-3456-7890)
                    </div>
                </div>

                <form method="POST" action="{{ route('customer.returns.ship', $return->return_number) }}" enctype="multipart/form-data" class="space-y-4 bg-white p-5 rounded-xl border border-purple-200">
                    @csrf
                    <h4 class="font-bold text-sm text-neutral-800">Unggah Bukti Resi Pengiriman J&T</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                Nomor Resi J&T Express <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="tracking_number" 
                                required 
                                placeholder="Contoh: JNT1234567890" 
                                class="w-full h-10 px-3 rounded-lg border border-neutral-300 text-xs font-mono"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                Foto Struk Resi J&T <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="file" 
                                name="shipping_proof" 
                                required 
                                accept="image/*"
                                class="w-full text-xs text-neutral-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-100 file:text-purple-800 cursor-pointer"
                            >
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2 rounded-lg bg-primary-700 hover:bg-primary-800 text-white font-bold text-xs shadow-xs transition">
                            Kirim Informasi Resi
                        </button>
                    </div>
                </form>
            </div>
        @elseif($return->status === \App\Enums\ReturnStatus::DalamPenyelesaian)
            @if($return->solution === 'replacement' && $return->replacementShipment())
                <!-- Konfirmasi Penerimaan Penggantian Barang -->
                <div class="p-6 rounded-xl bg-teal-50 border border-teal-200 font-body space-y-4">
                    <div>
                        <h3 class="font-display text-base font-bold text-teal-950">Barang Pengganti Telah Dikirim!</h3>
                        <p class="text-xs text-teal-800 mt-1">
                            Tim kami telah mengirimkan barang pengganti via <strong>J&T Express</strong> dengan No. Resi: 
                            <strong class="font-mono">{{ $return->replacementShipment()->tracking_number }}</strong>
                        </p>
                        @if($return->replacementShipment()->shipping_proof_path)
                            <div class="mt-2">
                                <a 
                                    href="{{ route('secure.proof', ['type' => 'return_shipment', 'id' => $return->replacementShipment()->id, 'filename' => basename($return->replacementShipment()->shipping_proof_path)]) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-teal-300 text-teal-900 text-xs font-semibold hover:bg-teal-100 transition"
                                >
                                    Lihat Foto Resi Pengganti
                                </a>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('customer.returns.confirm-replacement', $return->return_number) }}" enctype="multipart/form-data" class="space-y-3 bg-white p-5 rounded-xl border border-teal-200">
                        @csrf
                        <h4 class="font-bold text-sm text-neutral-800">Konfirmasi Penerimaan Barang Pengganti</h4>
                        <p class="text-xs text-neutral-600">
                            Jika paket barang pengganti telah sampai dan sesuai, silakan konfirmasi untuk menyelesaikan proses retur.
                        </p>

                        <div>
                            <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                Foto Bukti Barang Pengganti (Opsional)
                            </label>
                            <input 
                                type="file" 
                                name="received_proof" 
                                accept="image/*"
                                class="w-full text-xs text-neutral-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-teal-100 file:text-teal-800 cursor-pointer"
                            >
                        </div>

                        <label class="flex items-start gap-2 cursor-pointer pt-1">
                            <input type="checkbox" name="agreement" value="1" required class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-700 focus:ring-primary-500">
                            <span class="text-xs text-neutral-700">Saya menyatakan telah menerima barang pengganti dalam kondisi baik dan sesuai pesanan.</span>
                        </label>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shadow-xs transition">
                                Konfirmasi Barang Pengganti Diterima
                            </button>
                        </div>
                    </form>
                </div>
            @elseif($return->solution === 'refund' && $return->refund)
                <!-- Konfirmasi Penerimaan Dana Refund -->
                <div class="p-6 rounded-xl bg-teal-50 border border-teal-200 font-body space-y-4">
                    <div>
                        <h3 class="font-display text-base font-bold text-teal-950">Dana Refund Telah Ditransfer!</h3>
                        <p class="text-xs text-teal-800 mt-1">
                            Dana sebesar <strong>Rp{{ number_format($return->refund->amount, 0, ',', '.') }}</strong> telah ditransfer ke rekening:
                            <br>{{ $return->refund->bank_name }} - {{ $return->refund->account_number }} (a/n {{ $return->refund->account_name }})
                            <br>Ref: <strong class="font-mono">{{ $return->refund->payment_reference }}</strong>
                        </p>
                        @if($return->refund->proof_path)
                            <div class="mt-2">
                                <a 
                                    href="{{ route('secure.proof', ['type' => 'refund_proof', 'id' => $return->refund->id, 'filename' => basename($return->refund->proof_path)]) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-teal-300 text-teal-900 text-xs font-semibold hover:bg-teal-100 transition"
                                >
                                    Lihat Bukti Transfer Refund
                                </a>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('customer.returns.confirm-refund', $return->return_number) }}" class="space-y-3 bg-white p-5 rounded-xl border border-teal-200">
                        @csrf
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="checkbox" name="agreement" value="1" required class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-primary-700 focus:ring-primary-500">
                            <span class="text-xs text-neutral-700">Saya menyatakan bahwa dana pengembalian telah masuk ke rekening saya sesuai nominal di atas.</span>
                        </label>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shadow-xs transition">
                                Konfirmasi Dana Refund Diterima
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endif

        <!-- Main Content: Items & Evidences -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-body">
            <div class="lg:col-span-2 space-y-6">
                <!-- Produk yang Diretur -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 overflow-hidden">
                    <div class="px-6 py-4 border-b border-neutral-200">
                        <h2 class="font-display text-base font-bold text-primary-700">Rincian Produk Diretur</h2>
                    </div>
                    <div class="p-6 divide-y divide-neutral-100">
                        @foreach($return->items as $retItem)
                            <div class="py-3 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-bold text-neutral-800 text-sm">{{ $retItem->orderItem->product_name }}</p>
                                    <p class="text-neutral-500 font-mono">{{ $retItem->orderItem->variant_label }}</p>
                                    <p class="text-neutral-600 mt-0.5">Jumlah: <strong>{{ $retItem->quantity }} pcs</strong></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-neutral-800">Rp{{ number_format($retItem->subtotal, 0, ',', '.') }}</p>
                                    <p class="text-[11px] text-neutral-400">@ Rp{{ number_format($retItem->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Berkas Bukti: Video Unboxing & Foto -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <h2 class="font-display text-base font-bold text-primary-700">Berkas Bukti Pengajuan</h2>

                    @if($return->unboxingVideo())
                        <div>
                            <span class="text-xs font-bold text-neutral-700 block mb-2">Video Unboxing:</span>
                            <div class="rounded-xl overflow-hidden bg-black max-w-md">
                                <video 
                                    controls 
                                    preload="metadata"
                                    class="w-full max-h-72"
                                    src="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $return->unboxingVideo()->id, 'filename' => basename($return->unboxingVideo()->file_path)]) }}"
                                >
                                    Browser Anda tidak mendukung tag video HTML5.
                                </video>
                            </div>
                        </div>
                    @endif

                    @if($return->photos()->isNotEmpty())
                        <div>
                            <span class="text-xs font-bold text-neutral-700 block mb-2">Foto Bukti Fisik:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($return->photos() as $photo)
                                    <a 
                                        href="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $photo->id, 'filename' => basename($photo->file_path)]) }}" 
                                        target="_blank" 
                                        class="aspect-square rounded-lg border border-neutral-200 overflow-hidden bg-neutral-100 hover:opacity-90 transition block"
                                    >
                                        <img 
                                            src="{{ route('secure.proof', ['type' => 'return_evidence', 'id' => $photo->id, 'filename' => basename($photo->file_path)]) }}" 
                                            alt="Foto Bukti" 
                                            class="w-full h-full object-cover"
                                        >
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Status Timeline & Reason Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-3 text-xs">
                    <h3 class="font-display text-sm font-bold text-primary-700 border-b border-neutral-100 pb-2">Informasi Masalah</h3>
                    <div>
                        <span class="text-neutral-400 uppercase tracking-wider text-[10px] block font-semibold">Alasan Retur</span>
                        <p class="font-bold text-neutral-800 mt-0.5">{{ $return->reasonLabel() }}</p>
                    </div>
                    <div>
                        <span class="text-neutral-400 uppercase tracking-wider text-[10px] block font-semibold">Deskripsi</span>
                        <p class="text-neutral-700 mt-0.5 leading-relaxed bg-canvas-sunken p-2.5 rounded-lg border border-neutral-200/60">{{ $return->description }}</p>
                    </div>
                </div>

                <!-- Riwayat Status Timeline -->
                <div class="bg-white rounded-xl border border-neutral-200 shadow-1 p-6 space-y-4">
                    <h3 class="font-display text-sm font-bold text-primary-700 border-b border-neutral-100 pb-2">Riwayat Perkembangan</h3>
                    <div class="space-y-4">
                        @foreach($return->statusHistories as $log)
                            <div class="flex items-start gap-3 text-xs">
                                <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[10px]">
                                    ✓
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-neutral-800">{{ ucfirst(str_replace('_', ' ', $log->to_status)) }}</span>
                                        <span class="text-[10px] text-neutral-400">{{ $log->created_at->translatedFormat('d M H:i') }}</span>
                                    </div>
                                    @if($log->note)
                                        <p class="text-neutral-600 mt-0.5">{{ $log->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>
