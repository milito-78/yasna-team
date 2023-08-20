<?php

namespace App\Services\Orders\Entities;

use App\Models\Enums\PaymentGatewayEnum;

class TransactionCreateInput
{
    public function __construct(
        public int $order_id,
        public string $uuid,
        public int $price,
        public PaymentGatewayEnum $gateway,
    )
    {
    }

}
