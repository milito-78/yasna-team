<?php

namespace App\Services\Products\Entities;

use App\Models\Enums\ProductChangeReasonsEnum;

class ProductsChangeInput
{
    public function __construct(
        public array $products,
        public ProductChangeReasonsEnum $reason,
        public ?string $reasonable_type = null,
        public ?int $reasonable_id = null,
    )
    {
    }

    public function toArray() : array
    {
        return [
            "products"          => $this->products,
            'reason'            => $this->reason,
            'reasonable_type'   => $this->reasonable_type,
            'reasonable_id'     => $this->reasonable_id,
        ];
    }
}
