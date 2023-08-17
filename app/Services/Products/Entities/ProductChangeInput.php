<?php

namespace App\Services\Products\Entities;

use App\Models\Enums\ProductChangeReasonsEnum;

class ProductChangeInput
{
    public function __construct(
        public int $product_id,
        public int $count,
        public ProductChangeReasonsEnum $reason,
        public ?string $reasonable_type = null,
        public ?int $reasonable_id = null,
    )
    {
    }

    public function toArray() : array
    {
        return [
            'product_id'        => $this->product_id,
            'count'             => $this->count,
            'reason'            => $this->reason,
            'reasonable_type'   => $this->reasonable_type,
            'reasonable_id'     => $this->reasonable_id,
        ];
    }
}
