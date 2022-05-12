<?php

namespace App\QueryFilter\Searches;

use App\QueryFilter\Search;

class PaymentsSearch extends Search
{
    protected function applySearch($builder)
    {
        $search = request('search');
        $allowed = ['course.teacher', 'student', 'course.groups'];

        if (strpos(request('search'), ':') !== false) {
            $builder = $this->applySearchRelation($builder, $allowed, "payment");
        } else {
            $builder->where(function ($query) use ($search) {
                $query->whereRelation('course', 'title', 'LIKE', "%{$search}%")
                    ->orWhereRelation('course.teacher', 'name', 'LIKE', "%{$search}%")
                    ->orWhereRelation('student', 'name', 'LIKE', "%{$search}%")
                    ->orWhere('ref', 'LIKE', "%{$search}%")
                    ->orWhere('amount_payed', 'LIKE', "%{$search}%")
                    ->orWhere('paid_at', 'LIKE', "%{$search}%");
            });
        }

        return $builder;
    }
}
