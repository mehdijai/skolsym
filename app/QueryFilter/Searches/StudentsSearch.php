<?php

namespace App\QueryFilter\Searches;

use App\QueryFilter\Search;

class StudentsSearch extends Search
{
    protected function applySearch($builder)
    {
        $search = request('search');
        $allowed = ['groups', 'groups.course', 'groups.course.teacher'];

        if (strpos($search, ':') !== false) {
            $builder = $this->applySearchRelation($builder, $allowed, "student", "groups");
        } else {
            $builder->where(function ($query) use ($search) {
                $query->whereRelation('groups', 'title', 'LIKE', "%{$search}%")
                    ->orWhereRelation('groups.course', 'title', 'LIKE', "%{$search}%")
                    ->orWhereRelation('groups.course.teacher', 'name', 'LIKE', "%{$search}%")
                    ->orWhereRelation('payments', 'state', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('age', 'LIKE', "%{$search}%")
                    ->orWhere('grade', 'LIKE', "%{$search}%");
            });
        }

        return $builder;
    }
}
