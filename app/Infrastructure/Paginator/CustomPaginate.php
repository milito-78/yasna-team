<?php

namespace App\Infrastructure\Paginator;

use Illuminate\Support\Collection;

class CustomPaginate
{
    public function __construct(
        private readonly Collection $items,
        private readonly int $per_page,
        private readonly int $current_page,
        private readonly ?int $next_page,
    )
    {
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
