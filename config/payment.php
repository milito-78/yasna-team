<?php

use App\Infrastructure\Payments\Drivers\Quarkino\QuarkinoPaymentGateway;
use App\Infrastructure\Payments\Drivers\Milito\MilitoPaymentGateway;

return [

    "default" => env("PAYMENT_DEFAULT","milito"),

    "drivers" => [
        "milito" => [
            "class" => MilitoPaymentGateway::class,
            "url" => env("PAYMENT_MILITO_URL"),
            "token" => env("PAYMENT_MILITO_TOKEN"),
            "callback" => env("PAYMENT_MILITO_CALLBACK_URL"),
        ],
        "quarkino" => [
            "class" => QuarkinoPaymentGateway::class,
            "url" => env("PAYMENT_QUARKINO_URL"),
            "token" => env("PAYMENT_QUARKINO_TOKEN"),
            "callback" => env("PAYMENT_QUARKINO_CALLBACK_URL"),
        ]
    ]
];
