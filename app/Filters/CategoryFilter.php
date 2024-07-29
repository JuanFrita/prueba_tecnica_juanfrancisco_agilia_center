<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CategoryFilter
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function filter(Builder $query)
    {
        $query->whereHas('categories', function ($q) {
            $q->where('categories.id', $this->id);
        });
    }
}
