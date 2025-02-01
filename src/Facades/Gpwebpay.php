<?php

declare(strict_types=1);

namespace Codehub\Gpwebpay\Facades;

use Illuminate\Support\Facades\Facade;

class Gpwebpay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Codehub\Gpwebpay\Gpwebpay::class;
    }
}
