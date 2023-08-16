<?php

namespace App\Models;

use App\Models\Enums\ProductStatusesEnum;
use App\Models\Traits\FilterScopeTrait;
use App\Services\Products\Entities\ProductEntity;
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
    use HasFactory,FilterScopeTrait;

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
        return $query->where("status_id" , "!=", ProductStatusesEnum::Inactive->value);
    }

    public function scopeName($query,string $name)
    {
        return $query->where("name" , "LIKE" , "%" . $name . "%");
    }


    public function changes(): HasMany
    {
        return $this->hasMany(ProductChange::class);
    }


    public function toEntity() : ProductEntity
    {
        return new ProductEntity(
            $this->id,
            $this->name,
            $this->old_price,
            $this->price,
            $this->quantity,
            $this->status,
            $this->image,
            $this->created_at,
            $this->updated_at
        );
    }

    public static function fromEntity(ProductEntity $productEntity) : Product
    {
        $product               = new Product();
        $product->id           = $productEntity->id;
        $product->name         = $productEntity->name;
        $product->old_price    = $productEntity->old_price;
        $product->price        = $productEntity->price;
        $product->status       = $productEntity->status;
        $product->quantity     = $productEntity->quantity;
        $product->status_id    = $productEntity->status->value;
        $product->image        = $productEntity->image;
        $product->created_at   = $productEntity->created_at;
        $product->updated_at   = $productEntity->updated_at;
        return $product;
    }
}
