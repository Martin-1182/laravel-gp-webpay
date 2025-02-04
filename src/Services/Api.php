<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services;

use Exception;
use Websystem\Gpwebpay\Services\Exceptions\PaymentResponseException;
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

    /**
     * @throws PaymentResponseException
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
