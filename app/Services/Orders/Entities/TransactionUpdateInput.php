<?php

namespace App\Services\Orders\Entities;

use App\Models\Enums\TransactionStatusEnum;

class TransactionUpdateInput
{
    public function __construct(
        public string $uuid,
        public TransactionStatusEnum $status,
        public ?string $tracking_code
    )
    {
    }
}
