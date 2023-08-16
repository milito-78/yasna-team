<?php

namespace App\Infrastructure\Paginator;

use Illuminate\Support\Collection;

class CustomSimplePaginate
{
    public function __construct(
        private readonly Collection $items,
        private readonly int $per_page,
        private readonly int $current_page,
        private readonly ?int $next_page,
    )
    {
    }


    /**
     * @return Collection
     */
    public function items():Collection
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function perPage():int
    {
        return $this->per_page;
    }

    /**
     * @return int
     */
    public function currentPage():int
    {
        return $this->current_page;
    }

    /**
     * @return ?int
     */
    public function nextPage():?int
    {
        return $this->next_page;
    }

    /**
     * @return bool
     */
    public function nextPageExist() : bool
    {
        return !is_null($this->next_page);
    }

    public function toArray(): array
    {
        return [
            "items"         => $this->items,
            "per_page"      => $this->per_page,
            "current_page"  => $this->current_page,
            "next_page"     => $this->next_page,
            "count"         => $this->items->count()
        ];
    }
}
