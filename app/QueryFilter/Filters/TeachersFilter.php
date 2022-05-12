<?php

namespace App\QueryFilter\Filters;

use App\Const\StateLists;
use App\QueryFilter\Filter;

class TeachersFilter extends Filter
{
    protected function applyFilter($builder)
    {
        switch (request('filter')) {
            case StateLists::TEACHER['ACTIVE']:
                $builder->where('state', 'active')->where('archived', false);
                break;
            case StateLists::TEACHER['REMOVED']:
                $builder->where('state', 'removed')->where('archived', false);
                break;
            case 'archived':$builder->where('archived', true);
                break;
        }

        return $builder;
    }
}
