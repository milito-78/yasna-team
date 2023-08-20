<?php

namespace App\Infrastructure\Payments\Models;

class PurchaseResult
{
    public function __construct(
        private readonly string $tracking_code,
        private readonly string $redirect_path,
        private readonly int $code,
        private readonly bool $success,
        private readonly array $error = []
    )
    {
    }

    /**
     * @return string
     */
    public function getTrackingCode(): string
    {
        return $this->tracking_code;
    }

    /**
     * @return string
     */
    public function getRedirectPath(): string
    {
        return $this->redirect_path;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getError(): array
    {
        return $this->error;
    }


}
