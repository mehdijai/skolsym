<?php

namespace App\QueryFilter\Searches;

use App\QueryFilter\Search;

class GroupsSearch extends Search
{
    protected function applySearch($builder)
    {
        $search = request('search');
        $allowed = ['course', 'course.teacher', 'students'];

        if (strpos(request('search'), ':') !== false) {
            $builder = $this->applySearchRelation($builder, $allowed, "group", "students");
        } else {
            $builder->where(function ($query) use($search) {
                $query->whereRelation('course', 'title', 'LIKE', "%{$search}%")
                    ->orWhereRelation('course.teacher', 'name', 'LIKE', "%{$search}%")
                    ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        return $builder;
    }
}
