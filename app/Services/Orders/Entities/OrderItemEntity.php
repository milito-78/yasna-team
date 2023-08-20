<?php

namespace App\Services\Orders\Entities;

use Illuminate\Support\Carbon;

class OrderItemEntity
{
    public function __construct(
        public int $id,
        public int $order_id,
        public int $product_id,
        public ?int $old_price,
        public int $price,
        public int $count,
        public Carbon $created_at,
        public Carbon $updated_at,
    )
    {
    }

    public function toArray() : array
    {
        return [
            "id"            => $this->id,
            "order_id"      => $this->order_id,
            "product_id"    => $this->product_id,
            "old_price"     => $this->old_price,
            "price"         => $this->price,
            "count"         => $this->count,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at,
        ];
    }
}
