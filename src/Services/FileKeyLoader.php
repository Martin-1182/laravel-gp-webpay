<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Codehub\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Codehub\Gpwebpay\Services\Exceptions\SignerException;
use OpenSSLAsymmetricKey;

class FileKeyLoader implements KeyLoaderInterface
{
    private ?OpenSSLAsymmetricKey $privateKey = null;

    private ?OpenSSLAsymmetricKey $publicKey = null;

    public function __construct(
        private readonly string $privateKeyPath,
        private readonly string $privateKeyPassword,
        private readonly string $publicKeyPath
    ) {}

    /**
     * @throws SignerException
     */
    public function getPrivateKey(): OpenSSLAsymmetricKey
    {
        if ($this->privateKey === null) {
            $this->privateKey = $this->loadPrivateKey();
        }

        return $this->privateKey;
    }

    /**
     * @throws SignerException
     */
    public function getPublicKey(): OpenSSLAsymmetricKey
    {
        if ($this->publicKey === null) {
            $this->publicKey = $this->loadPublicKey();
        }

        return $this->publicKey;
    }

    /**
     * @throws SignerException
     */
    private function loadPrivateKey(): OpenSSLAsymmetricKey
    {
        $keyContents = file_get_contents($this->privateKeyPath);

        $privateKey = openssl_pkey_get_private($keyContents, $this->privateKeyPassword);
        if (! $privateKey) {
            throw new SignerException('Invalid private key or passphrase!');
        }

        return $privateKey;
    }

    /**
     * @throws SignerException
     */
    private function loadPublicKey(): OpenSSLAsymmetricKey
    {
        $keyContents = file_get_contents($this->publicKeyPath);

        $publicKey = openssl_pkey_get_public($keyContents);

        if (! $publicKey) {
            throw new SignerException('Invalid public key!');
        }

        return $publicKey;
    }
}
