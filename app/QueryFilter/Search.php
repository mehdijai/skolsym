<?php

namespace App\QueryFilter;

use Closure;

abstract class Search
{
    public function handle($request, Closure $next)
    {

        if (!request()->has('search')) {
            return $next($request);
        }

        $builder = $next($request);

        return $this->applySearch($builder);
    }

    abstract protected function applySearch($builder);

    protected function applySearchRelation($builder, $allowed, $model, $related_column = null)
    {

        $filter = explode(":", request('search'));
        if (in_array($filter[0], $allowed)) {
            if ($related_column != null) {
                $builder->whereRelation($filter[0],
                    $filter[0] == $related_column
                    ? $this->getColumnName($related_column)
                    : 'id', $filter[1]);
            } else {
                $builder->whereRelation($filter[0], 'id', $filter[1]);
            }
        } else if ($filter[0] === $model) {
            $builder->where('id', $filter[1]);
        }

        return $builder;
    }

    protected function getColumnName($model)
    {
        return substr($model, 0, -1) . '_id';
    }
}
