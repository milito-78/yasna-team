<?php

namespace App\Infrastructure\Payments\Models;

use Exception;
use Ramsey\Uuid\Uuid;

class Invoice
{
    /**
     * @param int $cost
     * @param string|null $tracking_code
     * @param string|null $uuid
     * @param array $data
     * @throws Exception
     */
    public function __construct(
        private int $cost = 0,
        private ?string $uuid = null,
        private ?string $tracking_code = null,
        private array $data = []
    )
    {
        $this->uuid();
    }

    /**
     * Set invoice uuid
     *
     * @param $uuid|null
     *
     * @throws Exception
     */
    public function uuid($uuid = null): void
    {
        if (empty($uuid)) {
            $uuid = Uuid::uuid4()->toString();
        }

        $this->uuid = $uuid;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return ?string
     */
    public function getTrackingCode() : ?string
    {
        return $this->tracking_code;
    }

    /**
     * @param string $tracking_code
     */
    public function setTrackingCode(string $tracking_code): void
    {
        $this->tracking_code = $tracking_code;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

}
