<?php

namespace App\Services\Orders\Entities;

class ValidateCallbackResult
{
    public function __construct(
        public bool $status,
        public ?OrderEntity $order,
    )
    {
    }
}
