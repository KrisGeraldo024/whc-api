<?php

return [
    'aub' => [
        'merchant_id' => env('AUB_MERCHANT_ID', '102550002764'),
        'merchant_key' => env('AUB_MERCHANT_KEY', 'ed5cd6346d0c2c6660e27e8d46c75977'),
        'gateway_url' => env('AUB_GATEWAY_URL', 'https://gateway.wepayez.com/pay/gateway'),
    ]
];
