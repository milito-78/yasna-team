<?php

namespace App\Models;

use App\Models\Enums\OrderStatusesEnum;
use App\Models\Traits\FilterScopeTrait;
use App\Services\Orders\Entities\OrderEntity;
use App\Services\Orders\Entities\OrderItemEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property int $total_price
 * @property int $pay_price
 * @property ?int $discount_price
 * @property OrderStatusesEnum $status
 * @property Collection $items
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Order extends Model
{
    use HasFactory,FilterScopeTrait;

    protected $guarded = [
        "id"
    ];

    protected $appends = [
        "status"
    ];


    public function getStatusAttribute() : OrderStatusesEnum
    {
        return OrderStatusesEnum::from($this->status_id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeDate($query,string $date)
    {
        return $query->whereDate("created_at" , $date );
    }

    public function scopeStatusId($query,int $status)
    {
        return $query->where("status_id" , $status);
    }

    public function scopeUserId($query,int $user)
    {
        return $query->where("user_id" , $user);
    }

    public function toEntity() : OrderEntity
    {
        return new OrderEntity(
            $this->id,
            $this->user_id,
            $this->total_price,
            $this->pay_price,
            $this->discount_price,
            $this->status,
            $this->created_at,
            $this->updated_at,
            $this->items->map(fn(OrderItem $item) => $item->toEntity())
        );
    }
}
