<?php

declare(strict_types=1);

use Codehub\Gpwebpay\Enums\Currency;
use Codehub\Gpwebpay\Enums\PaymentMethod;
use Codehub\Gpwebpay\Services\PaymentRequest;

it('generates the correct parameters', function () {
    $expectedParams = [
        'MERCHANTNUMBER' => '1234',
        'OPERATION' => 'CREATE_ORDER',
        'ORDERNUMBER' => 1234,
        'AMOUNT' => 980,
        'CURRENCY' => 978,
        'DEPOSITFLAG' => 0,
        'URL' => 'http://foo.bar',
        'PAYMETHOD' => 'CRD',
    ];

    $request = new PaymentRequest(
        orderNumber: 1234,
        amount: 9.8,
        currency: Currency::EUR,
        depositFlag: 0,
        url: 'http://foo.bar',
        paymentMethod: PaymentMethod::PAYMENT_CARD
    );

    $request->setMerchantNumber('1234');

    $generatedParams = $request->toArray();

    foreach ($expectedParams as $key => $value) {
        expect($generatedParams[$key])->toBe($value);
    }

    expect($generatedParams['DIGEST'] ?? null)->toBeNull();
});

it('generates correct parameters with different currency', function () {
    $request = new PaymentRequest(
        orderNumber: 1234,
        amount: 9.8,
        currency: Currency::USD, // testujeme inú menu
        depositFlag: 1,
        url: 'http://example.com',
        paymentMethod: PaymentMethod::PAYMENT_CARD
    );

    $request->setMerchantNumber('5678');

    $generatedParams = $request->toArray();

    expect($generatedParams)->toMatchArray([
        'MERCHANTNUMBER' => '5678',
        'OPERATION' => 'CREATE_ORDER',
        'ORDERNUMBER' => 1234,
        'AMOUNT' => 980, // $9.80 converted to cents
        'CURRENCY' => 840, // USD ISO code
        'DEPOSITFLAG' => 1,
        'URL' => 'http://example.com',
        'PAYMETHOD' => 'CRD',
    ]);
});

it('generates correct parameters with different payment methods', function () {
    $request = new PaymentRequest(
        orderNumber: 5678,
        amount: 50.0,
        currency: Currency::EUR,
        depositFlag: 1,
        url: 'http://example.com/callback',
        paymentMethod: PaymentMethod::PAYSAFECARD
    );

    $request->setMerchantNumber('1234');

    $generatedParams = $request->toArray();

    expect($generatedParams)->toMatchArray([
        'MERCHANTNUMBER' => '1234',
        'OPERATION' => 'CREATE_ORDER',
        'ORDERNUMBER' => 5678,
        'AMOUNT' => 5000, // €50.00 converted to cents
        'CURRENCY' => 978, // EUR
        'DEPOSITFLAG' => 1,
        'URL' => 'http://example.com/callback',
        'PAYMETHOD' => 'PAYSAFECARD', // bank account method
    ]);
});

it('handles payments with large amounts', function () {
    $request = new PaymentRequest(
        orderNumber: 5678,
        amount: 123456.78, // veľká suma
        currency: Currency::EUR,
        depositFlag: 1,
        url: 'http://example.com/payment',
        paymentMethod: PaymentMethod::PAYMENT_CARD
    );

    $request->setMerchantNumber('1234');

    $generatedParams = $request->toArray();

    expect($generatedParams)->toMatchArray([
        'MERCHANTNUMBER' => '1234',
        'OPERATION' => 'CREATE_ORDER',
        'ORDERNUMBER' => 5678,
        'AMOUNT' => 12345678, // veľká suma v centoch
        'CURRENCY' => 978,
        'DEPOSITFLAG' => 1,
        'URL' => 'http://example.com/payment',
        'PAYMETHOD' => 'CRD',
    ]);
});

it('correctly sets the digest', function () {
    $request = new PaymentRequest(
        orderNumber: 9999,
        amount: 20.0,
        currency: Currency::EUR,
        depositFlag: 0,
        url: 'http://example.com',
        paymentMethod: PaymentMethod::PAYMENT_CARD
    );

    $request->setMerchantNumber('4321');
    $request->setDigest('SAMPLEDIGESTVALUE');

    $generatedParams = $request->toArray();

    expect($generatedParams['DIGEST'])->toBe('SAMPLEDIGESTVALUE');
});
