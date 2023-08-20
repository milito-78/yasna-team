<?php

namespace App\Services\Orders\Entities;

use Illuminate\Contracts\Support\Arrayable;

class OrderCreateInput implements Arrayable
{
    /**
     * @param int $user
     * @param int $total_price
     * @param int $pay_price
     * @param int|null $discount_price
     * @param array<OrderItemCreateInput> $products
     */
    public function __construct(
        public int $user,
        public int  $total_price,
        public int  $pay_price,
        public ?int  $discount_price,
        public array $products,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "user_id"       => $this->user,
            "total_price"   => $this->total_price,
            "pay_price"     => $this->pay_price,
            "discount_price"=> $this->discount_price,
            "products"      => $this->products
        ];
    }
}
