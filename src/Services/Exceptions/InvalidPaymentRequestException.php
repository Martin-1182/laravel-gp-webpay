<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Services\Exceptions;

use Exception;

class InvalidPaymentRequestException extends Exception
{
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}
