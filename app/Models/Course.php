<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $appends = ['month_revenue'];

    protected function MonthRevenue(): Attribute
    {
        return new Attribute(
            get:function () {
                $groups = $this->groups()
                    ->get()
                    ->append('month_revenue')
                    ->toArray();

                $paidByGroups = array_map(function ($p) {
                    return $p['month_revenue'];
                }, $groups);

                $paidByCourse = array_reduce($paidByGroups, function ($c, $i) {
                    $c += $i;
                    return $c;
                });

                return $paidByCourse;
            },
        );
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'course_id', 'id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'course_id', 'id');
    }
}
