<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Websystem\Gpwebpay\Enums\Currency;
use Websystem\Gpwebpay\Gpwebpay;

it('registers Gpwebpay singleton via service provider', function () {
    Config::set('webpay.private_key_path', __DIR__.'/keys/test_key.pem');
    Config::set('webpay.public_key_path', __DIR__.'/keys/test_cert.pem');
    Config::set('webpay.private_key_password', 'changeit');
    Config::set('webpay.merchant_number', '123456');
    Config::set('webpay.url', 'https://webpay.url');

    File::shouldReceive('exists')
        ->with('/fake/path/private_key.pem')
        ->andReturn(true);

    File::shouldReceive('exists')
        ->with('/fake/path/public_key.pem')
        ->andReturn(true);

    File::shouldReceive('isReadable')
        ->with('/fake/path/private_key.pem')
        ->andReturn(true);

    File::shouldReceive('isReadable')
        ->with('/fake/path/public_key.pem')
        ->andReturn(true);

    $gpwebpay = app(Gpwebpay::class);

    expect($gpwebpay)->toBeInstanceOf(Gpwebpay::class);
});

it('creates a payment request URL via registered singleton', function () {
    Config::set('webpay.private_key_path', __DIR__.'/keys/test_key.pem');
    Config::set('webpay.public_key_path', __DIR__.'/keys/test_cert.pem');
    Config::set('webpay.private_key_password', 'changeit');
    Config::set('webpay.merchant_number', '123456');
    Config::set('webpay.url', 'https://webpay.url');

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('isReadable')->andReturn(true);

    $gpwebpay = app(Gpwebpay::class);

    $paymentData = [
        'orderNumber' => 1001,
        'amount' => 150.50,
        'currency' => Currency::EUR,
        'depositFlag' => 1,
        'returnUrl' => 'https://example.com/callback',
    ];

    $requestUrl = $gpwebpay->createPaymentRequestUrl($paymentData);

    expect($requestUrl)->toBeString()
        ->and($requestUrl)->toContain('https://webpay.url')
        ->and($requestUrl)->toContain('MERCHANTNUMBER=123456')
        ->and($requestUrl)->toContain('ORDERNUMBER=1001')
        ->and($requestUrl)->toContain('AMOUNT=15050')
        ->and($requestUrl)->toContain('CURRENCY=978')
        ->and($requestUrl)->toContain('DEPOSITFLAG=1')
        ->and($requestUrl)->toContain('URL=https%3A%2F%2Fexample.com%2Fcallback');
});
