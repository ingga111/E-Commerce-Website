<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DOKU Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Supported modes: 'sandbox' or 'production'
    |
    */
    'mode' => env('DOKU_MODE', 'sandbox'),

    'client_id' => env('DOKU_CLIENT_ID', 'DOKU-MCH-001'),
    'secret_key' => env('DOKU_SECRET_KEY', 'doku_key_38c723c0313f489690377e75b81cde1a'),
    'public_key' => env('DOKU_PUBLIC_KEY'),
    'public_key_path' => storage_path('app/doku/public_key.pem'),

    'urls' => [
        'sandbox' => 'https://api-sandbox.doku.com',
        'production' => 'https://api.doku.com',
    ],

    // Expiry duration in minutes (e.g. 1440 for 24 hours)
    'expiry_minutes' => (int) env('DOKU_EXPIRY_MINUTES', 1440),
];
