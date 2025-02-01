<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Codehub\Gpwebpay\Enums\Currency;
use Codehub\Gpwebpay\Enums\PaymentMethod;

class PaymentRequest
{
    private array $params;

    public function __construct(
        private readonly int $orderNumber,
        private readonly float $amount,
        private readonly Currency $currency,
        private readonly int $depositFlag,
        private readonly string $url,
        private readonly PaymentMethod $paymentMethod = PaymentMethod::PAYMENT_CARD
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
            'PAYMETHOD' => $this->paymentMethod->value,
        ];
    }

    public function setDigest(string $digest): void
    {
        $this->params['DIGEST'] = $digest;
    }

    public function setMerchantNumber(string $merchantNumber): void
    {
        $this->params['MERCHANTNUMBER'] = $merchantNumber;
    }

    public function toArray(): array
    {
        return $this->params;
    }
}
