<?php

namespace App\Models;

use App\Models\Enums\OrderStatusesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $total_price
 * @property int $pay_price
 * @property ?int $discount_price
 * @property OrderStatusesEnum $status
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Order extends Model
{
    use HasFactory;

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
}
