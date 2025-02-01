<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay;

use Websystem\Gpwebpay\Services\Api;
use Websystem\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Websystem\Gpwebpay\Services\Exceptions\SignerException;
use Websystem\Gpwebpay\Services\PaymentRequest;
use Websystem\Gpwebpay\Services\Signer;
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
