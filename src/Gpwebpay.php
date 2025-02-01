<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay;

use Codehub\Gpwebpay\Services\Api;
use Codehub\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Codehub\Gpwebpay\Services\Exceptions\SignerException;
use Codehub\Gpwebpay\Services\PaymentRequest;
use Codehub\Gpwebpay\Services\Signer;
use InvalidArgumentException;

class Gpwebpay
{
    private Api $api;

    public function __construct(KeyLoaderInterface $keyLoader, string $merchantNumber, string $webpayUrl)
    {
        $signer = new Signer($keyLoader);
        $this->api = new Api($merchantNumber, $webpayUrl, $signer);
    }

    /**
     * @throws SignerException
     */
    public function createPaymentRequestUrl($paymentData): string
    {
        $this->validatePaymentData($paymentData);

        $paymentRequest = new PaymentRequest(
            $paymentData['orderNumber'],
            $paymentData['amount'],
            $paymentData['currency'],
            $paymentData['depositFlag'],
            $paymentData['returnUrl'],
        );

        return $this->api->createPaymentRequestUrl($paymentRequest);
    }

    private function validatePaymentData(array $paymentData): void
    {
        $requiredFields = [
            'orderNumber',
            'amount',
            'currency',
            'depositFlag',
            'returnUrl',
        ];

        foreach ($requiredFields as $field) {
            if (empty($paymentData[$field])) {
                throw new InvalidArgumentException("Missing or empty required field: '{$field}'");
            }
        }
    }
}
