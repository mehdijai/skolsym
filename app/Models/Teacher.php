<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $table = "teachers";
    protected $fillable = [
        'name',
        'email',
        'phone',
        'state',
        'archived',
        'archived_at',
    ];
    // protected $appends = ['month_revenue'];

    // protected function MonthRevenue(): Attribute
    // {
    //     return new Attribute(
    //         get:function () {
    //             $coursesIDs = collect($this->courses()->select('id')->get()->toArray())->flatten();
    //             $currentMonthPayments = Payment::whereIn('course_id', $coursesIDs)
    //                 ->currentMonth()
    //                 ->get()
    //                 ->append('teacher_revenue')
    //                 ->toArray();

    //             $revenues = array_map(function ($p) {
    //                 return $p['teacher_revenue'];
    //             }, $currentMonthPayments);

    //             return array_reduce($revenues, function ($c, $i) {
    //                 $c += $i;
    //                 return $c;
    //             });
    //         },
    //     );
    // }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id', 'id');
    }
}
