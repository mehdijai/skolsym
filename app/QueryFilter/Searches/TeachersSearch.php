<?php

namespace App\QueryFilter\Searches;

use App\QueryFilter\Search;

class TeachersSearch extends Search
{
    protected function applySearch($builder)
    {
        $search = request('search');

        return $builder->where(function ($query) use($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }
}
