<?php

namespace App\Infrastructure\Payments\Dirvers\Quarkino;

use App\Infrastructure\Payments\Contracts\PaymentMethod;
use App\Infrastructure\Payments\Exceptions\InvalidConfigException;
use App\Infrastructure\Payments\Models\Invoice;
use App\Infrastructure\Payments\Models\PurchaseResult;

class QuarkinoPaymentGateway implements PaymentMethod
{

    /**
     *
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(
        private readonly array $config
    )
    {
        $this->checkConfig();
    }

    /**
     * Check config not empty
     *
     * @throws InvalidConfigException
     */
    private function checkConfig(): void
    {
        if (empty($this->config)){
            throw new InvalidConfigException();
        }
    }

    public function startPayment(Invoice $invoice): PurchaseResult
    {
        return new PurchaseResult(
            "",
            "http://localhost/quarkino",
            200,
            true
        );
    }

    public function inquiryPayment(string $uuid): bool
    {
        // TODO: Implement inquiryPayment() method.
    }
}
