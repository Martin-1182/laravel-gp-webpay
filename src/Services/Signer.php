<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use Websystem\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Websystem\Gpwebpay\Services\Exceptions\SignerException;

readonly class Signer
{
    public function __construct(
        private KeyLoaderInterface $keyLoader
    ) {}

    /**
     * @throws SignerException
     */
    public function sign(array $params): string
    {
        $data = $this->createSignedData($params);

        $privateKeyResource = $this->keyLoader->getPrivateKey();
        if (! openssl_sign($data, $signature, $privateKeyResource)) {
            throw new SignerException('Failed to sign data.');
        }

        return base64_encode($signature);
    }

    /**
     * @throws SignerException
     */
    public function verify(array $params, string $digest): bool
    {
        $data = $this->createSignedData($params);
        $publicKeyResource = $this->keyLoader->getPublicKey();

        $result = openssl_verify($data, base64_decode($digest), $publicKeyResource);
        if ($result !== 1) {
            throw new SignerException('Verification failed.');
        }

        return true;
    }

    private function createSignedData(array $params): string
    {
        return implode('|', $params);
    }
}
