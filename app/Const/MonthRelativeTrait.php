<?php
namespace App\Const;

trait MonthRelativeTrait
{
    public function scopeCurrentMonth($query)
    {
        $query->where('created_at', '>', now()->subMonth());
    }

    public function scopePreviousMonth($query)
    {
        $query->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()]);
    }
}
