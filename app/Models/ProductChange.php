<?php

namespace App\Models;

use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use App\Services\Products\Entities\ProductChangeEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $product_id
 * @property Product $product
 * @property int $count
 * @property ProductChangeReasonsEnum $reason
 * @property int $reason_id
 * @property ProductChangeTypesEnum $type
 * @property ProductChangeStatusesEnum $status
 * @property ?string $reasonable_type
 * @property ?int $reasonable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class ProductChange extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "type" => ProductChangeTypesEnum::class,
        "status" => ProductChangeStatusesEnum::class,
    ];

    protected $appends = [
        "reason"
    ];

    public function getReasonAttribute() : ProductChangeReasonsEnum
    {
        return ProductChangeReasonsEnum::from($this->reason_id);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reasonable()
    {
        return $this->morphTo("reasonable");
    }


    public function toEntity() : ProductChangeEntity
    {
        return new ProductChangeEntity(
            $this->product_id,
            $this->count,
            $this->reason,
            $this->type,
            $this->status,
            $this->reasonable_type,
            $this->reasonable_id,
            $this->created_at,
            $this->updated_at,
        );
    }

    public static function fromEntity(ProductChangeEntity $productEntity) : ProductChange
    {
        $product                    = new ProductChange();
        $product->product_id        = $productEntity->product_id;
        $product->count             = $productEntity->count;
        $product->reason            = $productEntity->reason;
        $product->reason_id         = $productEntity->reason->value;
        $product->type              = $productEntity->type;
        $product->status            = $productEntity->status;
        $product->reasonable_type   = $productEntity->reasonable_type;
        $product->reasonable_id     = $productEntity->reasonable_id;
        $product->created_at        = $productEntity->created_at;
        $product->updated_at        = $productEntity->updated_at;
        return $product;
    }

}
