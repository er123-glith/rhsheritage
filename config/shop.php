<?php

return [
    'low_stock_threshold'    => (int) env('SHOP_LOW_STOCK_THRESHOLD', 5),
    'owner_email'            => env('SHOP_OWNER_EMAIL', 'owner@rhsheritage.example'),
    'owner_initial_password' => env('SHOP_OWNER_INITIAL_PASSWORD', 'password'),
];
