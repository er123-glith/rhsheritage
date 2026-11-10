<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppLinkGenerator
{
    /**
     * Generate URL wa.me untuk pesanan.
     * Jika URL terlalu panjang (>2000 karakter), gunakan pesan ringkas.
     */
    public function forOrder(Order $order): string
    {
        $destinationNumber = ltrim(config('whatsapp.destination_number', '6285728979999'), '+');
        $maxLength = (int) config('whatsapp.max_url_length', 2000);

        $builder = app(WhatsAppMessageBuilder::class);

        // Coba pesan panjang (2 lapis) dahulu
        $message = $builder->buildMessage($order);
        $url = 'https://wa.me/' . $destinationNumber . '?text=' . rawurlencode($message);

        // Jika terlalu panjang, gunakan pesan ringkas
        if (strlen($url) > $maxLength) {
            $message = $builder->buildShortMessage($order);
            $url = 'https://wa.me/' . $destinationNumber . '?text=' . rawurlencode($message);
        }

        return $url;
    }

    /**
     * Generate URL wa.me generik (tanpa pesan) untuk tombol kontak umum.
     */
    public function forContact(?string $greeting = null): string
    {
        $destinationNumber = ltrim(config('whatsapp.destination_number', '6285728979999'), '+');

        if ($greeting) {
            return 'https://wa.me/' . $destinationNumber . '?text=' . rawurlencode($greeting);
        }

        return 'https://wa.me/' . $destinationNumber;
    }
}
