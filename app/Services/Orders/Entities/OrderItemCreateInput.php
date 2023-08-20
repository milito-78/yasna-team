<?php

namespace App\Services\Orders\Entities;

use App\Services\Products\Entities\ProductEntity;
use Illuminate\Contracts\Support\Arrayable;

class OrderItemCreateInput implements Arrayable
{

    /**
     * @param int $product_id
     * @param int $price
     * @param int|null $old_price
     * @param int $count
     */
    public function __construct(
        public int  $product_id,
        public int  $price,
        public ?int  $old_price,
        public int $count,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "product_id" => $this->product_id,
            "price" => $this->price,
            "old_price" => $this->old_price,
            "count" => $this->count,
        ];
    }


}
