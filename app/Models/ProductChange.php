<?php

namespace App\Models;

use App\Models\Enums\ProductChangeReasonsEnum;
use App\Models\Enums\ProductChangeStatusesEnum;
use App\Models\Enums\ProductChangeTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $product_id
 * @property Product $product
 * @property int $count
 * @property ProductChangeReasonsEnum $reason
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


}
