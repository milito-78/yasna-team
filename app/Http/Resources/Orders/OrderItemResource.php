<?php

namespace App\Http\Resources\Orders;

use App\Http\Resources\Products\ProductResource;
use App\Services\Orders\Entities\OrderItemEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var OrderItemEntity $item
         */
        $item = $this->resource;
        return [
            "id"            => $item->id,
            "order_id"      => $item->order_id,
            "old_price"     => $item->old_price,
            "price"         => $item->price,
            "count"         => $item->count,
            "product"       => new ProductResource($item->product),
            "created_at"    => resourceDateTimeFormat($item->created_at),
            "updated_at"    => resourceDateTimeFormat($item->updated_at),
        ];

    }
}
