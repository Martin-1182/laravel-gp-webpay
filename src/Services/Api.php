<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

use Exception;

readonly class Api
{
    public function __construct(
        private string $merchantNumber,
        private string $webPayUrl,
        private Signer $signer
    ) {}

    public function createPaymentRequestUrl(PaymentRequest $request): string
    {
        return $this->webPayUrl.'?'.http_build_query($this->createPaymentParam($request));
    }

    public function createPaymentParam(PaymentRequest $request): array
    {
        $request->setMerchantNumber($this->merchantNumber);
        $params = $request->getSignParams();
        $request->setDigest($this->signer->sign($params));

        return $request->getParams();
    }

    /**
     * @throws Exception
     */
    public function verifyPaymentResponse(PaymentResponse $response): void
    {
        try {
            $responseParams = $response->getParams();
            $this->signer->verify($responseParams, $response->getDigest());

            $responseParams['MERCHANTNUMBER'] = $this->merchantNumber;

            $this->signer->verify($responseParams, $response->getDigest1());
        } catch (SignerException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($response->hasError() !== false) {
            $prcode = $response->getParams()['prcode'];
            $srcode = $response->getParams()['srcode'];
            throw new PaymentResponseException(
                $prcode,
                $srcode,
                "Response has an error. {$prcode}:{$srcode}"
            );
        }
    }
}
