<?php

namespace App\Models;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $table = "students";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'age',
        'grade',
        'state',
        'archived',
        'archived_at',
    ];

    protected $appends = ['month_paid'];

    protected function MonthPaid(): Attribute
    {
        return new Attribute(
            get:function () {

                $currentMonthPayments = $this->payments()
                    ->currentMonth()
                    ->get()
                    ->toArray();

                $revenues = array_map(function ($p) {
                    if ($p['state'] == 'paid') {
                        return $p['amount_payed'];
                    }
                }, $currentMonthPayments);

                return array_reduce($revenues, function ($c, $i) {
                    $c += $i;
                    return $c;
                });
            },
        );
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)->using(GroupStudent::class)->withPivot('certificate');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }
}
