<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Codehub\Gpwebpay\Gpwebpay
 */
class Gpwebpay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Codehub\Gpwebpay\Gpwebpay::class;
    }
}
