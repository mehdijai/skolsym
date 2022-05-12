<?php

namespace App\QueryFilter\Filters;

use App\Const\StateLists;
use App\QueryFilter\Filter;

class StudentsFilter extends Filter
{
    protected function applyFilter($builder)
    {

        if (in_array(request('filter'), array_values(StateLists::STUDENT))) {
            $builder->where('state', request('filter'));
        } else if (in_array(request('filter'), array_values(StateLists::PAYMENT))) {
            $builder->whereRelation('payments', 'created_at', '>', now()->subMonth())
                ->whereRelation('payments', 'state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $builder->where('archived', true);
        }

        return $builder;
    }
}
