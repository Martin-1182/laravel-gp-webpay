<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use Websystem\Gpwebpay\Enums\Currency;
use Websystem\Gpwebpay\Enums\PaymentMethod;

class PaymentRequest
{
    private array $params;

    public function __construct(
        private readonly int $orderNumber,
        private readonly float $amount,
        private readonly Currency $currency,
        private readonly int $depositFlag,
        private readonly string $url,
        private readonly AddInfo $addInfo,
        private readonly PaymentMethod $paymentMethod = PaymentMethod::PAYMENT_CARD,

    ) {
        $this->params = $this->initializeBaseParams();
    }

    private function initializeBaseParams(): array
    {
        return [
            'MERCHANTNUMBER' => '',
            'OPERATION' => 'CREATE_ORDER',
            'ORDERNUMBER' => $this->orderNumber,
            'AMOUNT' => (int) ($this->amount * 100),
            'CURRENCY' => $this->currency->value,
            'DEPOSITFLAG' => $this->depositFlag,
            'URL' => $this->url,
            'ADDINFO' => $this->addInfo->toXml(),
            'PAYMETHOD' => $this->paymentMethod->value,
        ];
    }

    public function setDigest(string $digest): static
    {
        $this->params['DIGEST'] = $digest;

        return $this;
    }

    public function getSignParams(): array
    {
        return array_filter($this->params, fn (string $key) => $key !== 'LANG', ARRAY_FILTER_USE_KEY);
    }

    public function setMerchantNumber(string $merchantNumber): static
    {
        $this->params['MERCHANTNUMBER'] = $merchantNumber;

        return $this;
    }

    public function setDescription($value): static
    {
        $this->params['DESCRIPTION'] = $value;

        return $this;
    }

    public function setParam($key, $value): static
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function toArray(): array
    {
        return $this->params;
    }
}
