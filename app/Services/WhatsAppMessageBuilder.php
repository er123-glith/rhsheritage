<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppMessageBuilder
{
    /**
     * Format Rupiah: Rp1.250.000 (tanpa spasi setelah Rp, titik ribuan)
     */
    public function rupiah(float|int $amount): string
    {
        return 'Rp' . number_format((int) $amount, 0, ',', '.');
    }

    /**
     * Bangun payload array untuk disimpan ke orders.payload_json (arsip database).
     */
    public function buildPayload(Order $order): array
    {
        $payload = [
            'order_number'   => $order->order_number,
            'customer'       => [
                'name'  => $order->customer_name,
                'phone' => $order->customer_phone,
            ],
            'shipping'       => [
                'address'   => trim(implode(', ', array_filter([
                    $order->address_line,
                    $order->city,
                    $order->province,
                    $order->postal_code,
                ]))),
                'zone_name' => $order->shipping_zone_name ?? $order->shippingZone?->name ?? 'Belum ditentukan',
                'note'      => $order->note,
            ],
            'items'          => $order->orderItems->map(fn($item) => [
                'product'       => $item->product_name,
                'variant'       => $item->variant_label,
                'unit_price'    => $item->unit_price,
                'quantity'      => $item->quantity,
                'subtotal'      => $item->subtotal,
            ])->toArray(),
            'summary'        => [
                'subtotal'      => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total'         => $order->total,
                'weight_gram'   => $order->total_weight_gram,
            ],
            'created_at'     => $order->created_at?->toIso8601String(),
        ];

        return $this->sanitizeUtf8($payload);
    }

    private function sanitizeUtf8(mixed $data): mixed
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        if (is_array($data)) {
            return array_map([$this, 'sanitizeUtf8'], $data);
        }

        return $data;
    }

    /**
     * Bangun pesan WhatsApp formal, bersih, dan profesional (tanpa icon/emoji dan tanpa JSON).
     */
    public function buildMessage(Order $order): string
    {
        $createdAt = $order->created_at ?? now();
        $timeFormatted = $createdAt->format('d/m/Y H:i');
        $dateText = $createdAt->translatedFormat('l, d F Y');
        $zoneName = $order->shipping_zone_name ?? $order->shippingZone?->name ?? 'Pengiriman Standar';
        $customerEmail = $order->customer_email ?? $order->user?->email;

        $lines = [];

        // Header Resmi
        $lines[] = '*RHS HERITAGE — KONFIRMASI PESANAN*';
        $lines[] = '━━━━━━━━━━━━━━━━━━━━━━━━━';
        $lines[] = 'Halo Admin RHS Heritage,';
        $lines[] = 'Saya ingin mengonfirmasi pesanan yang telah saya buat melalui website RHS Heritage:';
        $lines[] = '';

        // Informasi Pesanan
        $lines[] = '*INFORMASI PESANAN*';
        $lines[] = "• No. Pesanan : *#{$order->order_number}*";
        $lines[] = "• Waktu Order : {$timeFormatted} WIB ({$dateText})";
        $lines[] = "• Status Awal : *{$order->status->label()}*";
        $lines[] = '';

        // Data Pemesan
        $lines[] = '*DATA PEMESAN*';
        $lines[] = "• Nama    : {$order->customer_name}";
        $lines[] = "• No. WA  : {$order->customer_phone}";
        if ($customerEmail) {
            $lines[] = "• Email   : {$customerEmail}";
        }
        $lines[] = '';

        // Alamat Pengiriman
        $lines[] = '*TUJUAN PENGIRIMAN*';
        $lines[] = "• Alamat  : {$order->address_line}";
        $lines[] = "• Kota    : {$order->city}, {$order->province} {$order->postal_code}";
        $lines[] = "• Zona    : {$zoneName}";
        if ($order->total_weight_gram) {
            $lines[] = "• Berat   : " . number_format($order->total_weight_gram, 0, ',', '.') . " gram";
        }
        if ($order->note) {
            $lines[] = "• Catatan : {$order->note}";
        }
        $lines[] = '';

        // Daftar Item
        $lines[] = '*RINCIAN PRODUK*';
        foreach ($order->orderItems as $i => $item) {
            $no = $i + 1;
            $lines[] = "{$no}. *{$item->product_name}*";
            $lines[] = "   - Varian   : {$item->variant_label}";
            $lines[] = "   - Jumlah   : {$item->quantity} unit × {$this->rupiah($item->unit_price)}";
            $lines[] = "   - Subtotal : {$this->rupiah($item->subtotal)}";
        }
        $lines[] = '';

        // Rincian Biaya & Total
        $lines[] = '*RINGKASAN PEMBAYARAN*';
        $lines[] = "• Subtotal Produk : {$this->rupiah($order->subtotal)}";
        $lines[] = "• Ongkos Kirim    : {$this->rupiah($order->shipping_cost)}";
        $lines[] = '━━━━━━━━━━━━━━━━━━━━━━━━━';
        $lines[] = "• *TOTAL TAGIHAN*  : *{$this->rupiah($order->total)}*";
        $lines[] = '';

        // Instruksi Selanjutnya
        $lines[] = 'Mohon informasikan ketersediaan stok produk dan rekening transfer resmi RHS Heritage (BCA / Mandiri / BRI) untuk penyelesaian pembayaran.';
        $lines[] = '';
        $lines[] = '*Lacak Pesanan Mandiri:*';
        $lines[] = url('/lacak-pesanan') . '?order=' . urlencode($order->order_number);
        $lines[] = '';
        $lines[] = 'Terima kasih atas pelayanannya.';
        $lines[] = '_RHS Heritage — Melestarikan Keanggunan Busana Nusantara_';

        // Hanya sertakan JSON jika secara eksplisit diaktifkan di konfigurasi
        if (config('whatsapp.include_json_block', false)) {
            $lines[] = '';
            $lines[] = '--- DATA JSON (untuk arsip) ---';
            $lines[] = json_encode($this->buildPayload($order), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return implode("\n", $lines);
    }

    /**
     * Versi pesan ringkas formal untuk wa.me URL jika teks panjang melebihi batas URL browser (tanpa icon/emoji).
     */
    public function buildShortMessage(Order $order): string
    {
        $lines = [];
        $lines[] = '*RHS HERITAGE — KONFIRMASI PESANAN*';
        $lines[] = 'Halo Admin RHS Heritage, saya ingin mengonfirmasi pesanan saya:';
        $lines[] = "• No. Pesanan : *#{$order->order_number}*";
        $lines[] = "• Nama        : {$order->customer_name} ({$order->customer_phone})";
        $lines[] = "• Total       : *{$this->rupiah($order->total)}*";
        $lines[] = 'Mohon informasi ketersediaan stok dan rekening resmi transfer pembayaran.';
        $lines[] = 'Lacak Pesanan: ' . url('/lacak-pesanan') . '?order=' . urlencode($order->order_number);
        $lines[] = 'Terima kasih!';

        return implode("\n", $lines);
    }
}
