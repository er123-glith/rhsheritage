<?php

return [
    // 0857-2897-9999 dalam format internasional tanpa tanda plus
    'destination_number' => env('WHATSAPP_NUMBER', '6285728979999'),
    'include_json_block' => env('WHATSAPP_INCLUDE_JSON', true),
    'max_url_length'     => (int) env('WHATSAPP_MAX_URL_LENGTH', 4000),
];
