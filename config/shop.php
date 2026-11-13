<?php

return [
    'low_stock_threshold'    => (int) env('SHOP_LOW_STOCK_THRESHOLD', 5),
    'owner_email'            => env('SHOP_OWNER_EMAIL', 'owner@rhsheritage.example'),
    'owner_initial_password' => env('SHOP_OWNER_INITIAL_PASSWORD', 'password'),

    // Administrator Account Configuration
    'admin_name'             => env('ADMIN_NAME', 'RHS Heritage Admin'),
    'admin_email'            => env('ADMIN_EMAIL', 'admin@rhsheritage.id'),
    'admin_password'         => env('ADMIN_PASSWORD'),
];
