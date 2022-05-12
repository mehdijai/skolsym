<?php

namespace App\QueryFilter;

use App\Const\StateLists;
use Closure;

abstract class Filter
{
    public function handle($request, Closure $next)
    {

        if (!request()->has('filter')) {
            return $next($request);
        }

        $builder = $next($request);

        return $this->applyFilter($builder);
    }

    protected function applyFilter($builder){
        if (in_array(request('filter'), StateLists::COURSE)) {
            $builder->where('state', request('filter'));
        }

        if (request('filter') == 'archived') {
            $builder->where('archived', true);
        }

        return $builder;
    }
}
