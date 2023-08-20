<?php

namespace App\Models;

use App\Services\Orders\Entities\OrderItemEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property ?int $old_price
 * @property int $price
 * @property int $count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [
        "id"
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function toEntity() :OrderItemEntity
    {
        return new OrderItemEntity(
            $this->id,
            $this->order_id,
            $this->product_id,
            $this->old_price,
            $this->price,
            $this->count,
            $this->created_at,
            $this->updated_at
        );
    }

}
