<?php

namespace App\Models;

use App\Const\StateLists;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory;

    protected $table = "payments";
    protected $fillable = [
        'ref',
        'student_id',
        'course_id',
        'amount_payed',
        'state',
        'paid_at',
        'archived',
        'archived_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
