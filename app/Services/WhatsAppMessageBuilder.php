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
     * Bangun payload array untuk disimpan ke orders.payload_json dan dikirim ke WA.
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
     * Bangun pesan WhatsApp format 2 lapis:
     * - Lapisan 1: Ringkasan teks rapi mudah dibaca manusia
     * - Lapisan 2: Blok data JSON untuk arsip
     */
    public function buildMessage(Order $order): string
    {
        $lines = [];

        $lines[] = '🛍️ *PESANAN BARU - RHS HERITAGE*';
        $lines[] = '━━━━━━━━━━━━━━━━━━━━';
        $lines[] = "📋 No. Pesanan: *{$order->order_number}*";
        $lines[] = "📅 Tanggal: {$order->created_at->format('d/m/Y H:i')}";
        $lines[] = '';
        $lines[] = '👤 *DATA PEMESAN*';
        $lines[] = "Nama  : {$order->customer_name}";
        $lines[] = "HP/WA : {$order->customer_phone}";
        $lines[] = '';
        $lines[] = '📦 *ALAMAT PENGIRIMAN*';
        $lines[] = $order->address_line;
        $lines[] = "{$order->city}, {$order->province} {$order->postal_code}";
        $lines[] = 'Zona  : ' . ($order->shipping_zone_name ?? $order->shippingZone?->name ?? '-');
        if ($order->note) {
            $lines[] = "Catatan: {$order->note}";
        }
        $lines[] = '';
        $lines[] = '🧾 *DAFTAR PESANAN*';
        foreach ($order->orderItems as $i => $item) {
            $no = $i + 1;
            $lines[] = "{$no}. {$item->product_name}";
            $lines[] = "   Varian : {$item->variant_label}";
            $lines[] = "   {$item->quantity} × {$this->rupiah($item->unit_price)} = {$this->rupiah($item->subtotal)}";
        }
        $lines[] = '';
        $lines[] = '💰 *RINCIAN BIAYA*';
        $lines[] = "Subtotal  : {$this->rupiah($order->subtotal)}";
        $lines[] = "Ongkir    : {$this->rupiah($order->shipping_cost)}";
        $lines[] = "━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "TOTAL     : *{$this->rupiah($order->total)}*";
        $lines[] = '';
        $lines[] = '_Pesan ini dibuat otomatis oleh sistem. Mohon balas untuk konfirmasi ketersediaan & detail pembayaran._';
        $lines[] = '';
        $lines[] = '--- DATA JSON (untuk arsip) ---';
        $lines[] = json_encode($this->buildPayload($order), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return implode("\n", $lines);
    }

    /**
     * Versi pesan ringkas untuk wa.me URL (tanpa blok JSON) — digunakan jika URL terlalu panjang.
     */
    public function buildShortMessage(Order $order): string
    {
        $lines = [];
        $lines[] = "🛍️ Halo, saya ingin konfirmasi pesanan *{$order->order_number}*";
        $lines[] = "Nama: {$order->customer_name} | HP: {$order->customer_phone}";
        $lines[] = "Total: {$this->rupiah($order->total)}";
        $lines[] = "Mohon konfirmasi ketersediaan dan detail pembayaran. Terima kasih!";

        return implode("\n", $lines);
    }
}
