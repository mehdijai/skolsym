<?php

namespace App\QueryFilter\Searches;

use App\QueryFilter\Search;

class CoursesSearch extends Search
{
    protected function applySearch($builder)
    {
        $search = request('search');
        $allowed = ['teacher', 'groups'];

        if (strpos($search, ':') !== false) {
            $builder = $this->applySearchRelation($builder, $allowed, "course");
        } else {
            $builder->where(function ($query) use ($search) {
                $query->whereRelation('teacher', 'name', 'LIKE', '%' . request('search') . '%')
                ->orWhere('title', 'LIKE', '%' . request('search') . '%')
                ->orWhere('period', 'LIKE', '%' . request('search') . '%')
                ->orWhere('price', 'LIKE', '%' . request('search') . '%')
                ->orWhere('payment_type', 'LIKE', '%' . request('search') . '%');
            });
        }

        return $builder;
    }
}
