<?php

namespace App\Services\Orders\Entities;

class OrderFilterInput
{
    public function __construct(
        public int $per_page = 15,
        public int $page = 1,
        public ?string $date = null,
        public ?int $status = null,
    )
    {
    }

    public function toArray():array
    {
        return [
            "status"    => $this->status,
            "date"      => $this->date,
        ];
    }
}
