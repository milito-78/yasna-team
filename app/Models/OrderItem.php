<?php

namespace App\Models;

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
 * @property int $pay_price
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
