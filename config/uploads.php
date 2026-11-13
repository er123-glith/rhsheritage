<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Centralized File Upload Limits (in kilobytes)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi batas ukuran upload terpusat untuk frontend dan backend
    | pada sistem RHS Heritage.
    |
    */

    'shipping_proof' => [
        'max_kb'         => 2048,           // 2 MB
        'max_bytes'      => 2048 * 1024,    // 2,097,152 bytes
        'max_mb'         => 2,
        'allowed_mimes'  => ['jpg', 'jpeg', 'png', 'webp'],
        'accept'         => 'image/jpeg,image/png,image/webp',
        'mimes_rule'     => 'mimes:jpg,jpeg,png,webp',
        'error_messages' => [
            'required' => 'Foto bukti struk resi pengiriman wajib diunggah.',
            'mimes'    => 'Foto bukti pengiriman harus berformat JPG, JPEG, PNG, atau WEBP.',
            'max'      => 'Ukuran foto bukti resi maksimal 2 MB.',
        ],
    ],

    'delivery_proof' => [
        'max_kb'         => 5120,           // 5 MB
        'max_bytes'      => 5120 * 1024,    // 5,242,880 bytes
        'max_mb'         => 5,
        'allowed_mimes'  => ['jpg', 'jpeg', 'png', 'webp'],
        'accept'         => 'image/jpeg,image/png,image/webp',
        'mimes_rule'     => 'mimes:jpg,jpeg,png,webp',
        'error_messages' => [
            'required' => 'Foto bukti penerimaan barang wajib diunggah.',
            'mimes'    => 'Foto bukti penerimaan harus berformat JPG, JPEG, PNG, atau WEBP.',
            'max'      => 'Ukuran foto bukti penerimaan maksimal 5 MB.',
        ],
    ],

    'unboxing_video' => [
        'max_kb'         => 102400,         // 100 MB
        'max_bytes'      => 102400 * 1024,  // 104,857,600 bytes
        'max_mb'         => 100,
        'allowed_mimes'  => ['mp4', 'mov', 'webm'],
        'accept'         => 'video/mp4,video/quicktime,video/webm',
        'mimes_rule'     => 'mimes:mp4,mov,webm',
        'error_messages' => [
            'required' => 'Berkas video unboxing wajib diunggah.',
            'mimes'    => 'Format video harus MP4, MOV, atau WEBM.',
            'max'      => 'Ukuran video unboxing maksimal 100 MB.',
        ],
    ],

    'return_photo' => [
        'max_kb'         => 5120,           // 5 MB
        'max_bytes'      => 5120 * 1024,    // 5,242,880 bytes
        'max_mb'         => 5,
        'allowed_mimes'  => ['jpg', 'jpeg', 'png', 'webp'],
        'accept'         => 'image/jpeg,image/png,image/webp',
        'mimes_rule'     => 'mimes:jpg,jpeg,png,webp',
        'error_messages' => [
            'required' => 'Foto bukti fisik kerusakan/ketidaksesuaian wajib diunggah.',
            'mimes'    => 'Foto bukti harus berformat JPG, JPEG, PNG, atau WEBP.',
            'max'      => 'Ukuran masing-masing foto bukti maksimal 5 MB.',
        ],
    ],
];

