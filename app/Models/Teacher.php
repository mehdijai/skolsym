<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['month_revenue'];

    protected function MonthRevenue(): Attribute
    {
        return new Attribute(
            get:function () {
                $courses = $this->courses()
                    ->get()
                    ->append('month_revenue')
                    ->toArray();

                $paidByCourse = array_map(function ($p) {
                    return $p['month_revenue'] * $p['teacher_percentage'];
                }, $courses);

                $paidByTeacher = array_reduce($paidByCourse, function ($c, $i) {
                    $c += $i;
                    return $c;
                });

                return $paidByTeacher;
            },
        );
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id', 'id');
    }
}
