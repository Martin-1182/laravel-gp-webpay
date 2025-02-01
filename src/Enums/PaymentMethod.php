<?php

declare(strict_types=1);

namespace Websystem\Gpwebpay\Enums;

enum PaymentMethod: string
{
    case APPLE_PAY = 'APAY';
    case EPS = 'EPS';
    case GOOGLE_PAY = 'GPAY';
    case KLARNA = 'KLARNA';
    case PAYMENT_CARD = 'CRD';
    case PAYSAFECARD = 'PAYSAFECARD';
    case PLATBA_24 = 'BTNCS';
    case SEPADIRECTDEBIT = 'SEPADIRECTDEBIT';
    case SOFORT = 'SOFORT';
}
