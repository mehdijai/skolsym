<?php

namespace App\Models;

use App\Const\RemoveModelTrait;
use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, RemoveModelTrait;

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

    protected $appends = ['month_paid', 'last_month_paid'];

    protected function getMonthPaidAttribute()
    {
        return $this->getAmountPaid();
    }

    protected function getLastMonthPaidAttribute()
    {
        return $this->getAmountPaid(true);
    }

    protected function getAmountPaid($previous = false)
    {
        $payments = $this->payments();

        if ($previous) {
            $payments->previousMonth();
        } else {
            $payments->currentMonth();
        }

        $MonthPayments = $payments->get()->toArray();

        $revenues = array_map(function ($p) {
            if ($p['state'] == 'paid') {
                return $p['amount_payed'];
            }
        }, $MonthPayments);

        return array_reduce($revenues, function ($c, $i) {
            $c += $i;
            return $c;
        }, 0);
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
