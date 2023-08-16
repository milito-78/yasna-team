<?php

namespace App\Models\Traits;


use App\Infrastructure\Filter\IFilterable;
use Illuminate\Database\Eloquent\Builder;

trait FilterScopeTrait
{
    public function scopeFilter(Builder $query ,array $filters): Builder
    {
        foreach ($filters as $name => $filter)
        {
            $query->$name($filter);
        }

        return $query;
    }
}
