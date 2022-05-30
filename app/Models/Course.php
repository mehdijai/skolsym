<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, RemoveModelTrait;

    protected $table = "courses";
    protected $fillable = [
        'teacher_id',
        'title',
        'price',
        'teacher_percentage',
        'period',
        'payment_type',
        'state',
        'archived',
        'archived_at',
    ];

    protected $appends = ['month_revenue', 'last_month_revenue'];

    protected function getMonthRevenueAttribute()
    {
        return $this->getAmountRevenue();
    }

    protected function getLastMonthRevenueAttribute()
    {
        return $this->getAmountRevenue(true);
    }

    protected function getAmountRevenue($previous = false)
    {
        $groups = $this->groups()->get();

        if ($previous) {
            $groups = $groups->append('month_revenue')->toArray();
        } else {
            $groups = $groups->append('last_month_revenue')->toArray();
        }

        $paidByGroups = array_map(function ($p) use ($previous) {
            if ($previous) {
                return $p['last_month_revenue'];
            } else {
                return $p['month_revenue'];
            }
        }, $groups);

        return array_reduce($paidByGroups, function ($c, $i) {
            $c += $i;
            return $c;
        });
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'course_id', 'id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
