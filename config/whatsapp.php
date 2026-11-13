<?php

return [
    // Nomor WhatsApp official RHS Heritage (format internasional tanpa tanda plus)
    'destination_number' => env('WHATSAPP_NUMBER', '6285728979999'),

    // Nonaktifkan blok JSON mentah pada pesan WhatsApp agar tampilan selalu profesional dan rapi
    'include_json_block' => env('WHATSAPP_INCLUDE_JSON', false),

    // Batas panjang URL wa.me yang aman untuk browser
    'max_url_length'     => (int) env('WHATSAPP_MAX_URL_LENGTH', 4000),
];
