<?php

namespace App\Services\Orders\Entities;

class SubmitOrderInput
{
    /**
     * @param int $user
     * @param array{'product_id': int, 'count': int, 'price': int, 'old_price': int|null} $products
     */
    public function __construct(
        public int $user,
        public array $products,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "user_id"       => $this->user,
            "products"      => $this->products
        ];
    }
}
