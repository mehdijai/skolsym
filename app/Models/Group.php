<?php

namespace App\Models;

use App\Models\Course;
use App\Models\GroupStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    protected $fillable = [
        'course_id',
        'title',
        'state',
        'archived',
        'archived_at',
    ];

    protected $appends = ['month_revenue'];

    protected function MonthRevenue(): Attribute
    {
        return new Attribute(
            get:function () {
                $students = $this->students()
                    ->get()
                    ->append('month_paid')
                    ->toArray();

                $paidByStudents = array_map(function ($p) {
                    return $p['month_paid'];
                }, $students);

                $paidByGroup = array_reduce($paidByStudents, function ($c, $i) {
                    $c += $i;
                    return $c;
                });

                return $paidByGroup;
            },
        );
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->using(GroupStudent::class)->withPivot('certificate');
    }
}
