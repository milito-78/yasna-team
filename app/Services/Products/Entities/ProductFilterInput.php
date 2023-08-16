<?php

namespace App\Services\Products\Entities;

class ProductFilterInput
{

    public function __construct(
        public int $per_page = 15,
        public int $page = 1,
        public ?string $name = null
    )
    {
    }

    public function toArray():array
    {
        return [
            "name" => $this->name
        ];
    }
}
