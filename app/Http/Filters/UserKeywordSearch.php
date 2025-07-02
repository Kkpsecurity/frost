<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UserKeywordSearch implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function (Builder $q) use ($value) {
            $q->where('fname', 'ilike', '%'.$value.'%')
              ->orWhere('lname', 'ilike', '%'.$value.'%')
              ->orWhere('email', 'ilike', '%'.$value.'%');
        });
    }
}
