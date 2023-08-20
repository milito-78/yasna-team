<?php

namespace App\Services\Orders\Entities;

class StartPaymentResult
{
    public function __construct(
        public TransactionEntity $transaction,
        public readonly string $redirect_path,
        public readonly bool $success,
    )
    {
    }
}
