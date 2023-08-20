<?php

namespace App\Models;

use App\Models\Enums\PaymentGatewayEnum;
use App\Models\Enums\TransactionStatusEnum;
use App\Services\Orders\Entities\TransactionEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    protected $appends = [
        "status",
        "payment_gateway",
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStatusAttribute() : TransactionStatusEnum
    {
        return TransactionStatusEnum::from($this->status_id);
    }

    public function getPaymentGatewayAttribute() : PaymentGatewayEnum
    {
        return PaymentGatewayEnum::from($this->payment_id);
    }


    public function toEntity() :TransactionEntity
    {
        return new TransactionEntity(
            $this->id,
            $this->uuid,
            $this->user_id,
            $this->price,
            $this->relationLoaded("transactionable") ?  $this->transactionable->toEntity() : null ,
            $this->tracking_code,
            $this->status,
            $this->payment_gateway,
            $this->received_at,
            $this->created_at,
            $this->updated_at,
        );
    }
}
