<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Codehub\Gpwebpay\Enums\Currency;
use Codehub\Gpwebpay\Enums\PaymentMethod;
use Codehub\Gpwebpay\Services\Exceptions\InvalidPaymentRequestException;

class PaymentRequest
{
    /**
     * Interné parametre na odoslanie do platobnej brány.
     */
    private array $params = [];

    /**
     * @throws InvalidPaymentRequestException
     */
    public function __construct(
        readonly int $orderNumber,
        readonly float $amount,
        readonly Currency $currency,
        readonly int $depositFlag,
        readonly string $url,
        readonly ?string $merOrderNumber = null,
        readonly ?string $md = null,
        readonly ?AddInfo $addInfo = null,
        readonly PaymentMethod $paymentMethod = PaymentMethod::PAYMENT_CARD
    ) {
        $this->initializeParams();
    }

    private function initializeParams(): void
    {
        $this->params = [
            'MERCHANTNUMBER' => '', // bude neskôr nastavené
            'OPERATION' => 'CREATE_ORDER',
            'ORDERNUMBER' => $this->orderNumber,
            'AMOUNT' => (int)($this->amount * 100), // konverzia na centy
            'CURRENCY' => $this->currency->value, // použitie hodnoty z enumu
            'DEPOSITFLAG' => $this->depositFlag,
            'URL' => $this->url,
            'PAYMETHOD' => $this->paymentMethod->value, // použitie hodnoty z enumu
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

    /**
     * Nastavenie `DIGEST` hodnoty požiadavky.
     */
    public function setDigest(string $digest): void
    {
        $this->params['DIGEST'] = $digest;
    }

    /**
     * Nastavenie obchodníckeho čísla.
     */
    public function setMerchantNumber(string $merchantNumber): void
    {
        $this->params['MERCHANTNUMBER'] = $merchantNumber;
    }

    /**
     * Nastavenie popisu transakcie.
     */
    public function setDescription(string $description): void
    {
        $this->params['DESCRIPTION'] = $description;
    }

    /**
     * Nastavenie jazyka.
     */
    public function setLang(string $lang): void
    {
        $this->params['LANG'] = $lang;
    }

    /**
     * Získanie parametrov, ktoré budú podpísané.
     */
    public function getSignParams(): array
    {
        $filterOutParams = ['LANG'];

        return array_filter(
            $this->params,
            fn (string $key) => !in_array($key, $filterOutParams, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Pridanie alebo prepísanie vlastného parametru.
     */
    public function setParam(string $key, string $value): void
    {
        $this->params[$key] = $value;
    }
}
