<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tripay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Supported modes: 'sandbox' or 'production'
    |
    */
    'mode' => env('TRIPAY_MODE', 'sandbox'),

    'api_key' => env('TRIPAY_API_KEY', 'DEV-sample-api-key'),
    'private_key' => env('TRIPAY_PRIVATE_KEY', 'sample-private-key'),
    'merchant_code' => env('TRIPAY_MERCHANT_CODE', 'T12345'),

    'urls' => [
        'sandbox' => 'https://tripay.co.id/api-sandbox',
        'production' => 'https://tripay.co.id/api',
    ],

    // Default expiry duration in hours
    'expiry_hours' => (int) env('TRIPAY_EXPIRY_HOURS', 24),
];
