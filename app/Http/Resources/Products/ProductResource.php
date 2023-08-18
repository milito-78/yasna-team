<?php

namespace App\Http\Resources\Products;

use App\Services\Products\Entities\ProductEntity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var ProductEntity $item
         */
        $item = $this->resource;
        return [
            "id" => $item->id,
            "name"=> $item->name,
            "old_price"=> $item->old_price,
            "price"=> $item->price,
            "quantity" => $item->quantity,
            "image"=>  getImageFullPath($item->image),
        ];
    }
}
