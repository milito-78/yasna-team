<?php

namespace App\Infrastructure\Payments;

enum PaymentMethodEnum: string
{
    case MilitoGateway = 'milito';

    case QuarkinoGateway = 'quarkino';
}
