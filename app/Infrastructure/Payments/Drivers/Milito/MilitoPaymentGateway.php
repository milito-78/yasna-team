<?php

namespace App\Infrastructure\Payments\Drivers\Milito;

use App\Infrastructure\Payments\Drivers\GatewayAbstract;
use App\Infrastructure\Payments\Models\InquiryResult;
use App\Infrastructure\Payments\Models\Invoice;
use App\Infrastructure\Payments\Models\PurchaseResult;
use Ramsey\Uuid\Uuid;

class MilitoPaymentGateway extends GatewayAbstract
{
    /**
     * @inheritdoc
     */
    public function startPayment(Invoice $invoice): PurchaseResult
    {
        return new PurchaseResult(
            "",
            $this->buildUrl(["price" => $invoice->getCost(), "uuid"=> $invoice->getUuid()]),
            200,
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function inquiryPayment(string $uuid): InquiryResult
    {
        $tracking_code = Uuid::uuid4()->toString() . '-' . time();

        return new InquiryResult(
            $uuid,
            $tracking_code,
            200,
            true
        );
    }
}
