<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services\Exceptions;

use Exception;

class PaymentResponseException extends Exception
{
    private int $prCode;

    private int $srCode;

    public function __construct(
        int $prCode,
        int $srCode = 0,
        string $message = '',
        ?Exception $previous = null
    ) {
        $this->prCode = $prCode;
        $this->srCode = $srCode;

        // Allow the parent exception to use the provided prCode as the error code for better consistency
        parent::__construct($message !== '' ? $message : $this->generateDefaultMessage(), $prCode, $previous);
    }

    public function getPrCode(): int
    {
        return $this->prCode;
    }

    public function getSrCode(): int
    {
        return $this->srCode;
    }

    /**
     * Generate a default exception message if none is provided
     */
    private function generateDefaultMessage(): string
    {
        return sprintf('Payment response error: PRCODE=%d, SRCODE=%d', $this->prCode, $this->srCode);
    }

    /**
     * Return a detailed report of the exception
     */
    public function getDetails(): string
    {
        return sprintf(
            'PRCODE: %d, SRCODE: %d, MESSAGE: %s',
            $this->prCode,
            $this->srCode,
            $this->getMessage()
        );
    }
}
