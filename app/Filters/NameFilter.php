<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class NameFilter
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function filter(Builder $query)
    {
        return $query->where('name', 'like', "%{$this->name}%");
    }
}
