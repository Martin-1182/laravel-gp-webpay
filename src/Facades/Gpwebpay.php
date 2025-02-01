<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Facades;

use Illuminate\Support\Facades\Facade;

class Gpwebpay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Websystem\Gpwebpay\Gpwebpay::class;
    }
}
