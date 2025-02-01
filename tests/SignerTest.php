<?php

declare(strict_types=1);

use Codehub\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Codehub\Gpwebpay\Services\Exceptions\SignerException;
use Codehub\Gpwebpay\Services\FileKeyLoader;
use Codehub\Gpwebpay\Services\Signer;

beforeEach(function () {
    $this->privateKeyPath = __DIR__ . '/keys/test_key.pem';
    $this->publicKeyPath = __DIR__ . '/keys/test_cert.pem';
    $this->keyPassword = 'changeit';

    $this->keyLoader = new FileKeyLoader($this->privateKeyPath, $this->keyPassword, $this->publicKeyPath);
    $this->signer = new Signer($this->keyLoader);
});

it('can create a valid signature', function () {
    $params = ['ORDERNUMBER' => 12345, 'AMOUNT' => 1000, 'CURRENCY' => 978];

    $digest = $this->signer->sign($params);

    expect($digest)->not->toBeEmpty()
        ->and($digest)->toBeString();
});

it('can verify a valid signature', function () {
    $params = ['ORDERNUMBER' => 12345, 'AMOUNT' => 1000, 'CURRENCY' => 978];
    $digest = $this->signer->sign($params);

    $isValid = $this->signer->verify($params, $digest);

    expect($isValid)->toBeTrue();
});

it('throws an exception for invalid private key', function () {
    $mockKeyLoader = $this->createMock(KeyLoaderInterface::class);

    $mockKeyLoader->method('getPrivateKey')
        ->will($this->throwException(new SignerException('Failed to load private key.')));

    $signer = new Signer($mockKeyLoader);
    $signer->sign(['test']);
})->throws(SignerException::class);
