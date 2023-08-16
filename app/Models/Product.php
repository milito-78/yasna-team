<?php

namespace App\Models;

use App\Models\Enums\ProductStatusesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property int $old_price
 * @property int $price
 * @property int $quantity
 * @property ProductStatusesEnum $status
 * @property int $status_id
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Product extends Model
{
    use HasFactory;

    protected $guarded = [
        "id"
    ];

    protected $appends = [
        "quantity",
        "status"
    ];

    public function getStatusAttribute() : ProductStatusesEnum
    {
        return ProductStatusesEnum::from($this->status_id);
    }

    public function getQuantityAttribute() : int
    {
        return $this->changes()->sum(DB::raw("count * type"));
    }

    public function scopeActiveProduct($query)
    {
        return $query->where("status_id" , ProductStatusesEnum::Active->value);
    }


    public function changes(): HasMany
    {
        return $this->hasMany(ProductChange::class);
    }

}
