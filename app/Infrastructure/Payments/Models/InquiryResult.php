<?php

namespace App\Infrastructure\Payments\Models;

class InquiryResult
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $tracking_code,
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
    public function getUuid(): string
    {
        return $this->uuid;
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
