<?php

namespace App\Services\Orders\Entities;

use App\Models\Enums\OrderStatusesEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OrderEntity
{
    /**
     * @param int $id
     * @param int $user_id
     * @param int $total_price
     * @param int $pay_price
     * @param int|null $discount_price
     * @param OrderStatusesEnum $status
     * @param Carbon $created_at
     * @param Carbon $updated_at
     * @param Collection<OrderItemEntity> $items
     * @param TransactionEntity|null $transaction
     */
    public function __construct(
        public int $id,
        public int $user_id,
        public int $total_price,
        public int $pay_price,
        public ?int $discount_price,
        public OrderStatusesEnum $status,
        public Carbon $created_at,
        public Carbon $updated_at,
        public Collection $items = new Collection(),
        public ?TransactionEntity $transaction = null,
    )
    {
    }

    public function toArray() : array
    {
        return [
            "id"                => $this->id,
            "user_id"           => $this->user_id,
            "total_price"       => $this->total_price,
            "pay_price"         => $this->pay_price,
            "discount_price"    => $this->discount_price,
            "status"            => $this->status,
            "created_at"        => $this->created_at,
            "updated_at"        => $this->updated_at,
        ];
    }
}
