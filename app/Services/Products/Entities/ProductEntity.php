<?php

namespace App\Services\Products\Entities;

use App\Models\Enums\ProductStatusesEnum;
use Illuminate\Support\Carbon;

class ProductEntity
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $old_price,
        public int $price,
        public int $quantity,
        public ProductStatusesEnum $status,
        public string $image,
        public Carbon $created_at,
        public Carbon $updated_at,
    )
    {
    }

    public function toArray() : array
    {
        return [
            "id"                => $this->id,
            "name"              => $this->name,
            "old_price"         => $this->old_price,
            "price"             => $this->price,
            "off_percentage"    => (($this->price - $this->old_price) / $this->price) * 100 ,
            "quantity"          => $this->quantity,
            "status"            => $this->status,
            "image"             => $this->image,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at,
        ];
    }

}
