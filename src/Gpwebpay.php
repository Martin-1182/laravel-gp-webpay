<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;
use Websystem\Gpwebpay\Services\AddInfo;
use Websystem\Gpwebpay\Services\Api;
use Websystem\Gpwebpay\Services\Contracts\KeyLoaderInterface;
use Websystem\Gpwebpay\Services\Exceptions\SignerException;
use Websystem\Gpwebpay\Services\PaymentRequest;
use Websystem\Gpwebpay\Services\PaymentResponse;
use Websystem\Gpwebpay\Services\Signer;

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
        // Validate required payment data
        $this->validatePaymentData($paymentData);

        // Load XML schema for AddInfo
        $schema = file_get_contents(storage_path('gpwebpay/GPwebpayAdditionalInfoRequest_v.5.xsd'));
        $minimalValues = AddInfo::createMinimalValues();

        // Prepare additional info
        $addInfo = $this->createAddInfo($schema, $minimalValues, $paymentData['addInfo']);


        $paymentRequest = new PaymentRequest(
            $paymentData['orderNumber'],
            $paymentData['amount'],
            $paymentData['currency'],
            $paymentData['depositFlag'],
            $paymentData['returnUrl'],
            $addInfo
        );

        // Set language if provided, otherwise use default locale
        $paymentRequest->setParam('LANG', $paymentData['lang'] ?? config('app.locale'));

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

    private function createAddInfo(string $schema, array $minimalValues, array $addInfoData): AddInfo
    {
        return new AddInfo(
            $schema,
            array_merge($minimalValues, [
                'cardholderInfo' => [
                    'cardholderDetails' => [
                        'name' => iconv('UTF-8', 'ASCII//TRANSLIT', $addInfoData['name']),
                        'email' => $addInfoData['email'],
                    ],
                ],
            ])
        );
    }

    private function validateResponseParams(array $responseParams): void
    {
        $requiredKeys = ['OPERATION', 'ORDERNUMBER', 'PRCODE', 'SRCODE', 'DIGEST', 'DIGEST1'];
        foreach ($requiredKeys as $key) {
            if (! array_key_exists($key, $responseParams)) {
                throw new InvalidArgumentException("Missing required response parameter: {$key}");
            }
        }
    }

    public function verifyPaymentResponse(array $responseParams): bool
    {
        try {
            $this->validateResponseParams($responseParams);

            $response = new PaymentResponse(
                $responseParams['OPERATION'],
                $responseParams['ORDERNUMBER'],
                (int) $responseParams['PRCODE'],
                (int) $responseParams['SRCODE'],
                $responseParams['DIGEST'],
                $responseParams['DIGEST1'],
                $responseParams['MERORDERNUM'] ?? null,
                $responseParams['RESULTTEXT'] ?? null,
                $responseParams['MD'] ?? null
            );

            $this->api->verifyPaymentResponse($response);

            return true;
        } catch (Throwable $th) {
            Log::error('WebpayService. Error! Can not verifying payment response: '.$th->getMessage());

            return false;
        }
    }
}
