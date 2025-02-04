<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use Websystem\Gpwebpay\Services\Exceptions\SignerException;

readonly class Api
{
    public function __construct(
        private string $merchantNumber,
        private string $webPayUrl,
        private Signer $signer
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
