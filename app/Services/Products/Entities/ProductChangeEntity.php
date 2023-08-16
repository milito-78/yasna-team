<?php

namespace App\Services\Products\Entities;

use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use Illuminate\Support\Carbon;

class ProductChangeEntity
{
    public Carbon $created_at;
    public Carbon $updated_at;
    public function __construct(
        public int $product_id,
        public int $count,
        public ProductChangeReasonsEnum $reason,
        public ProductChangeTypesEnum $type,
        public ProductChangeStatusesEnum $status,
        public ?string $reasonable_type = null,
        public ?int $reasonable_id = null,
        Carbon $created_at = null,
        Carbon $updated_at = null,
    )
    {
        $this->created_at = $created_at??now();
        $this->updated_at = $updated_at??now();
    }

    public function toArray() : array
    {
        return [
            "product_id" => $this->product_id,
            "count" => $this->count,
            "reason" => $this->reason,
            "type" => $this->type,
            "status" => $this->status,
            "reasonable_type" => $this->reasonable_type,
            "reasonable_id" => $this->reasonable_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
