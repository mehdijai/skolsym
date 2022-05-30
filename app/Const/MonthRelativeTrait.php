<?php
namespace App\Const;

use Illuminate\Database\Eloquent\Builder;

trait MonthRelativeTrait
{
    public function scopeCurrentMonth(Builder $query)
    {
        $query->whereBetween('created_at', [now()->firstOfMonth(), now()->lastOfMonth()]);
    }

    public function scopePreviousMonth(Builder $query)
    {
        $query->whereBetween('created_at', [now()->subMonths(2)->firstOfMonth(), now()->subMonths(2)->lastOfMonth()]);
    }
}
