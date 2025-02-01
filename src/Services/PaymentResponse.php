<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Services;

class PaymentResponse
{
    private array $params;

    private string $digest;

    private string $digest1;

    public function __construct(
        string $operation,
        string $orderNumber,
        ?string $merOrderNumber,
        int $prCode,
        int $srCode,
        ?string $resultText,
        string $digest,
        string $digest1,
        ?string $md
    ) {
        $this->params = $this->initializeParams(
            $operation,
            $orderNumber,
            $merOrderNumber,
            $prCode,
            $srCode,
            $resultText,
            $md
        );
        $this->digest = $digest;
        $this->digest1 = $digest1;
    }

    private function initializeParams(
        string $operation,
        string $orderNumber,
        ?string $merOrderNumber,
        int $prCode,
        int $srCode,
        ?string $resultText,
        ?string $md
    ): array {
        $params = [
            'operation' => $operation,
            'ordernumber' => $orderNumber,
            'prcode' => $prCode,
            'srcode' => $srCode,
        ];

        if ($merOrderNumber) {
            $params['merordernum'] = $merOrderNumber;
        }

        if ($md) {
            $params['md'] = $md;
        }

        if ($resultText) {
            $params['resulttext'] = $resultText;
        }

        return $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getDigest(): string
    {
        return $this->digest;
    }

    public function getDigest1(): string
    {
        return $this->digest1;
    }

    public function hasError(): bool
    {
        return $this->params['prcode'] !== 0 || $this->params['srcode'] !== 0;
    }

    public function getOperation(): string
    {
        return $this->params['operation'];
    }

    public function getOrderNumber(): string
    {
        return $this->params['ordernumber'];
    }

    public function getMerOrderNumber(): ?string
    {
        return $this->params['merordernum'] ?? null;
    }

    public function getPrCode(): int
    {
        return $this->params['prcode'];
    }

    public function getSrCode(): int
    {
        return $this->params['srcode'];
    }

    public function getResultText(): ?string
    {
        return $this->params['resulttext'] ?? null;
    }

    public function getMd(): ?string
    {
        return $this->params['md'] ?? null;
    }
}
