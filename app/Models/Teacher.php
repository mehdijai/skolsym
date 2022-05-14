<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory, RemoveModelTrait;

    protected $table = "teachers";
    protected $fillable = [
        'name',
        'email',
        'phone',
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
        $courses = $this->courses()->get();

        if ($previous) {
            $courses = $courses->append('month_revenue')->toArray();
        } else {
            $courses = $courses->append('last_month_revenue')->toArray();
        }

        $paidByCourses = array_map(function ($p) use ($previous) {
            if ($previous) {
                return $p['last_month_revenue'];
            } else {
                return $p['month_revenue'];
            }
        }, $courses);

        return array_reduce($paidByCourses, function ($c, $i) {
            $c += $i;
            return $c;
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id', 'id');
    }
}
