<?php
namespace App\Infrastructure\Payments\Contracts;


use App\Infrastructure\Payments\Exceptions\ServiceUnreachableException;
use App\Infrastructure\Payments\Models\Invoice;
use App\Infrastructure\Payments\Models\PurchaseResult;

interface PaymentMethod
{

    /**
     * Start a new payment
     *
     * @param Invoice $invoice
     * @return PurchaseResult
     * @throws ServiceUnreachableException
     */
    public function startPayment(Invoice $invoice): PurchaseResult;

    /**
     * Check and validate payment with uuid
     *
     * @param string $uuid
     * @return bool
     * @throws ServiceUnreachableException
     */
    public function inquiryPayment(string $uuid): bool;
}
