<?php

use App\Infrastructure\Payments\Dirvers\Quarkino\QuarkinoPaymentGateway;
use App\Infrastructure\Payments\Dirvers\Milito\MilitoPaymentGateway;

return [

    "default" => env("PAYMENT_DEFAULT","milito"),

    "drivers" => [
        "milito" => [
            "class" => MilitoPaymentGateway::class,
            "url" => env("PAYMENT_MILITO_URL"),
            "token" => env("PAYMENT_MILITO_TOKEN"),
        ],
        "quarkino" => [
            "class" => QuarkinoPaymentGateway::class,
            "url" => env("PAYMENT_QUARKINO_URL"),
            "token" => env("PAYMENT_QUARKINO_TOKEN"),
        ]
    ]
];
