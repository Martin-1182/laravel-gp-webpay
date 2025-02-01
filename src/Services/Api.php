<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use Websystem\Gpwebpay\Services\Exceptions\SignerException;

class Api
{
    public function __construct(
        private readonly string $merchantNumber,
        private readonly string $webPayUrl,
        private readonly Signer $signer
    ) {}

    /**
     * @throws SignerException
     */
    public function createPaymentRequestUrl(PaymentRequest $request): string
    {
        $queryParams = $this->createPaymentParam($request);

        return $this->webPayUrl.'?'.http_build_query($queryParams);
    }

    /**
     * @throws SignerException
     */
    public function createPaymentParam(PaymentRequest $request): array
    {
        $request->setMerchantNumber($this->merchantNumber);
        $signData = $request->toArray();
        $request->setDigest($this->signer->sign($signData));

        return $request->toArray();
    }
}
