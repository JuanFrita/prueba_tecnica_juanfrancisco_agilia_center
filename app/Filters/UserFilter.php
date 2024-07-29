<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function filter(Builder $query)
    {
        return $query->where('user_id', $this->id);
    }
}
