<?php

declare(strict_types=1);

return [
    'private_key_path' => env('GPWEBPAY_PRIVATE_KEY_PATH'),
    'private_key_password' => env('GPWEBPAY_PRIVATE_KEY_PASSWORD'),
    'public_key_path' => env('GPWEBPAY_PUBLIC_KEY_PATH'),
    'merchant_number' => env('GPWEBPAY_MERCHANT_NUMBER'),
    'url' => env('GPWEBPAY_URL'),
    'add_info_schema' => env('GPWEBPAY_ADD_INFO_SCHEMA'),
];
