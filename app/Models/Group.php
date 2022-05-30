<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use App\Models\Course;
use App\Models\GroupStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory, RemoveModelTrait;

    protected $table = 'groups';
    protected $fillable = [
        'course_id',
        'title',
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
        $students = $this->students()->get();

        if ($previous) {
            $students = $students->append('month_paid')->toArray();
        } else {
            $students = $students->append('last_month_paid')->toArray();
        }

        $paidByStudents = array_map(function ($p) use ($previous) {
            if ($previous) {
                return $p['last_month_paid'];
            } else {
                return $p['month_paid'];
            }
        }, $students);

        return array_reduce($paidByStudents, function ($c, $i) {
            $c += $i;
            return $c;
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->using(GroupStudent::class)->withPivot('certificate');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'group_id', 'id');
    }
}
