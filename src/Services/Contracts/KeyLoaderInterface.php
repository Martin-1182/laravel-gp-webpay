<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services\Contracts;

use OpenSSLAsymmetricKey;

interface KeyLoaderInterface
{
    public function getPrivateKey(): OpenSSLAsymmetricKey;

    public function getPublicKey(): OpenSSLAsymmetricKey;
}
