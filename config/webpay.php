<?php

declare(strict_types=1);

return [
    'private_key_path' => env('GPWEBPAY_PRIVATE_KEY_PATH', 'path/to/private_key.pem'),
    'private_key_password' => env('GPWEBPAY_PRIVATE_KEY_PASSWORD', 'password'),
    'public_key_path' => env('GPWEBPAY_PUBLIC_KEY_PATH', 'path/to/public_key.pem'),
    'merchant_number' => env('GPWEBPAY_MERCHANT_NUMBER', '123456'),
    'url' => env('GPWEBPAY_URL', 'https://example.com/webpay'),
];
