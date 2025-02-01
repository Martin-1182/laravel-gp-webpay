<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Codehub\Gpwebpay\Enums\Currency;
use Codehub\Gpwebpay\Enums\PaymentMethod;

class PaymentRequest
{
    private array $params = [];

    public function __construct(
        private int $orderNumber,
        private float $amount,
        private Currency $currency,
        private int $depositFlag,
        private string $url,
        private ?string $merOrderNumber = null,
        private ?string $md = null,
        private ?AddInfo $addInfo = null,
        private PaymentMethod $paymentMethod = PaymentMethod::PAYMENT_CARD
    ) {
        $this->initializeParams();
    }

    private function initializeParams(): void
    {
        $this->params = [
            'MERCHANTNUMBER' => '',
            'OPERATION' => 'CREATE_ORDER',
            'ORDERNUMBER' => $this->orderNumber,
            'AMOUNT' => (int) ($this->amount * 100),
            'CURRENCY' => $this->currency->value,
            'DEPOSITFLAG' => $this->depositFlag,
            'URL' => $this->url,
            'PAYMETHOD' => $this->paymentMethod->value,
        ];

        if ($this->merOrderNumber) {
            $this->params['MERORDERNUM'] = $this->merOrderNumber;
        }

        if ($this->md) {
            $this->params['MD'] = $this->md;
        }

        if ($this->addInfo) {
            $this->params['ADDINFO'] = $this->addInfo->toXml();
        }
    }

    public function setDigest(string $digest): void
    {
        $this->params['DIGEST'] = $digest;
    }

    public function setMerchantNumber(string $merchantNumber): void
    {
        $this->params['MERCHANTNUMBER'] = $merchantNumber;
    }

    public function setDescription(string $description): void
    {
        $this->params['DESCRIPTION'] = $description;
    }

    public function setLang(string $lang): void
    {
        $this->params['LANG'] = $lang;
    }

    public function getSignParams(): array
    {
        $filterOutParams = ['LANG'];

        return array_filter(
            $this->params,
            fn (string $key) => ! in_array($key, $filterOutParams, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function setParam(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }
}
