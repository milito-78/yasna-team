<?php

namespace App\Models\Enums;

use App\Infrastructure\Payments\PaymentMethodEnum;

enum PaymentGatewayEnum:int
{
    case MilitoPayment = 1;

    case QuarkinoPayment = 2;

    public static function getFromName(string $name): ?PaymentGatewayEnum
    {
        return match ($name){
            "milito" => self::MilitoPayment,
            "quarkino" => self::QuarkinoPayment,
            default => null
        };
    }

    public function toGatewayEnums(): ?PaymentMethodEnum
    {
        return match ($this){
            self::MilitoPayment => PaymentMethodEnum::MilitoGateway,
            self::QuarkinoPayment => PaymentMethodEnum::QuarkinoGateway,
            default => null
        };
    }

    public static function validationNames() : string
    {
        $strings = [];
        foreach(PaymentMethodEnum::cases() as $case) {
            $strings[] = $case->value;
        }
        return implode(",",$strings);
    }
}
