<?php

namespace App\Http\Resources\Orders;

use App\Services\Orders\Entities\OrderEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    private ?OrderItemResourceCollection $_items = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var OrderEntity $item
         */
        $item = $this->resource;
        return [
            "id"                => $item->id,
            "total_price"       => $item->total_price,
            "pay_price"         => $item->pay_price,
            "discount_price"    => $item->discount_price,
            "status"            => $item->status->value,
            "status_text"       => $item->status->name,
            "items"             => $this->whenNotNull($this->_items,$this->_items),
            "created_at"        => resourceDateTimeFormat($item->created_at),
            "updated_at"        => resourceDateTimeFormat($item->updated_at),
        ];
    }

    public function addItems(OrderItemResourceCollection $items): static
    {
        $this->_items = $items;
        return $this;
    }
}
