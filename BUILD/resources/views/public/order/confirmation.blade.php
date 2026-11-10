<x-layouts.public>
    <x-slot:title>Konfirmasi Pesanan #{{ $order->order_number }} — RHS HERITAGE</x-slot:title>

    <div class="max-w-3xl mx-auto space-y-8">
        <!-- Success Banner -->
        <div class="rounded-2xl bg-white p-6 sm:p-10 border border-neutral-200 shadow-2 text-center space-y-4" data-aos="zoom-in" data-aos-duration="650">
            <div class="w-16 h-16 rounded-full bg-success-bg text-success mx-auto flex items-center justify-center border-2 border-emerald-300 shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div data-aos="fade-up" data-aos-delay="150">
                <span class="inline-block px-3 py-1 rounded-full bg-primary-50 text-primary-800 font-body text-xs font-semibold uppercase tracking-wider mb-2">
                    Pesanan Berhasil Disimpan
                </span>
                <h1 class="font-display text-2xl sm:text-4xl font-bold text-neutral-800">
                    Terima Kasih, {{ $order->customer_name }}!
                </h1>
                <p class="font-body text-sm text-neutral-600 max-w-md mx-auto mt-2 leading-relaxed">
                    Pesanan Anda telah tercatat dalam sistem dengan nomor referensi unik di bawah ini:
                </p>
            </div>

            <!-- Order Number Badge -->
            <div class="inline-flex items-center gap-3 px-5 py-3 rounded-xl bg-canvas-sunken border border-neutral-200 text-neutral-800" data-aos="fade-up" data-aos-delay="200">
                <span class="font-body text-xs text-neutral-500 font-medium">Nomor Pesanan:</span>
                <span class="font-body font-bold text-lg sm:text-xl text-primary-700 tracking-wider font-mono">
                    {{ $order->order_number }}
                </span>
            </div>

            <!-- Crucial Action: WhatsApp Redirect -->
            <div class="pt-4 max-w-md mx-auto space-y-3" data-aos="fade-up" data-aos-delay="250">
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white font-body font-bold text-base sm:text-lg shadow-lg hover:shadow-xl transition-all duration-200 active:scale-[0.99]"
                >
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Kirim Pesanan ke WhatsApp</span>
                </a>

                <p class="font-body text-xs text-neutral-500">
                    Klik tombol di atas untuk mengirim rincian otomatis dan panduan pembayaran rekening resmi dari owner.
                </p>
            </div>
        </div>

        <!-- Next Steps Guidance -->
        <div class="rounded-xl bg-canvas-sunken p-6 border border-neutral-200/80 space-y-3" data-aos="fade-up" data-aos-duration="600">
            <h3 class="font-display text-base font-bold text-neutral-800">
                Langkah Selanjutnya:
            </h3>
            <ol class="font-body text-xs text-neutral-600 space-y-2 list-decimal list-inside leading-relaxed">
                <li>Klik tombol <strong>Kirim Pesanan ke WhatsApp</strong> untuk konfirmasi data ke owner RHS HERITAGE.</li>
                <li>Owner akan mengonfirmasi stok siap kirim beserta rekening transfer (BCA/Mandiri/BRI).</li>
                <li>Setelah melakukan pembayaran, kirimkan foto/tangkapan layar bukti transfer ke WhatsApp.</li>
                <li>Pesanan akan dipacking rapi dan nomor resi pengiriman akan diberikan melalui fitur pelacakan pesanan.</li>
            </ol>
        </div>

        <!-- Order Summary Breakdown -->
        <div class="rounded-2xl bg-white p-6 sm:p-8 border border-neutral-200 shadow-1 space-y-6" data-aos="fade-up" data-aos-duration="650">
            <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                <h3 class="font-display text-lg font-bold text-neutral-800">
                    Rincian Pesanan
                </h3>
                <x-status-badge :status="$order->status" />
            </div>

            <!-- Items -->
            <div class="divide-y divide-neutral-100">
                @foreach($order->orderItems as $item)
                    <div class="py-3 flex items-center justify-between font-body text-xs sm:text-sm">
                        <div>
                            <p class="font-semibold text-neutral-800">{{ $item->product_name }}</p>
                            <p class="text-neutral-500 text-xs">Varian: {{ $item->variant_label }} &times; {{ $item->quantity }} unit</p>
                        </div>
                        <span class="font-semibold text-neutral-800">
                            Rp{{ number_format((float) $item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Cost Summary -->
            <div class="pt-4 border-t border-neutral-200 space-y-2 font-body text-xs sm:text-sm text-neutral-600">
                <div class="flex justify-between">
                    <span>Subtotal Produk:</span>
                    <span class="font-semibold text-neutral-800">Rp{{ number_format((float) $order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Ongkos Kirim ({{ $order->shipping_zone_name }}):</span>
                    <span class="font-semibold text-neutral-800">Rp{{ number_format((float) $order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t border-neutral-200 text-base font-bold text-neutral-800">
                    <span>Total Tagihan:</span>
                    <span class="font-display text-xl text-primary-700">Rp{{ number_format((float) $order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Shipping Destination Details -->
            <div class="pt-4 border-t border-neutral-100">
                <h4 class="font-body text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">
                    Tujuan Pengiriman:
                </h4>
                <p class="font-body text-xs text-neutral-700 leading-relaxed">
                    <strong>{{ $order->customer_name }}</strong> ({{ $order->customer_phone }})<br>
                    {{ $order->address_line }}<br>
                    {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}<br>
                    @if($order->note)
                        <span class="italic text-neutral-500">Catatan: {{ $order->note }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs font-body pt-4" data-aos="fade-up">
            <a href="{{ route('home') }}" class="text-primary-700 font-semibold hover:underline">
                &larr; Kembali ke Beranda
            </a>
            <a href="{{ route('order.track') }}" class="text-neutral-600 hover:text-primary-700">
                Lacak Status Pesanan
            </a>
        </div>
    </div>
</x-layouts.public>
