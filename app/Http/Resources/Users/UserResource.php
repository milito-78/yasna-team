<?php

namespace App\Http\Resources\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var User $item
         */
        $item = $this->resource;

        return [
            "id"            => $item->id,
            "name"          => $item->name,
            "email"         => $item->email,
            "status"        => $item->status,
            "created_at"    => $item->created_at,
            "updated_at"    => $item->updated_at,
        ];
    }
}
