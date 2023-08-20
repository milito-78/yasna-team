<?php

namespace App\Services\Orders\Entities;

use App\Models\Enums\PaymentGatewayEnum;
use App\Models\Enums\TransactionStatusEnum;
use Illuminate\Support\Carbon;

class TransactionEntity
{
    public function __construct(
        public int $id,
        public string $uuid,
        public int $user_id,
        public int $price,
        public ?OrderEntity $order,
        public ?string $tracking_code,
        public TransactionStatusEnum $status,
        public PaymentGatewayEnum $gateway,
        public ?Carbon $received_at,
        public Carbon $created_at,
        public Carbon $updated_at,
    )
    {
    }
}
