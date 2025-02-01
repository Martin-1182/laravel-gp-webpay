<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Enums;

enum Currency: int
{
    case EUR = 978;
    case CZK = 203;
    case GBP = 826;
    case HUF = 348;
    case PLN = 985;
    case RUB = 643;
    case USD = 840;

    public static function isValid(int $currencyCode): bool
    {
        return in_array($currencyCode, array_column(self::cases(), 'value'), true);
    }
}
