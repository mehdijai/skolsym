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
        'student_id',
        'course_id',
        'amount_payed',
        'state',
        'paid_at',
        'archived',
        'archived_at',
    ];

    public static function pay($student, $course, $amount = null)
    {
        $payment = Payment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'amount_payed' => $amount ?? $course->price,
            'state' => StateLists::PAYMENT['PAID'],
            'paid_at' => now()
        ]);

        return $payment;
    }

    public static function get_status(Builder $student, Builder $course)
    {
        $last_payment = self::query()
            ->where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if($last_payment === null){
            return null;
        }

        Log::info(Carbon::now()->diffInMonths($last_payment->created_at));

        if (Carbon::now()->diffInMonths($last_payment->created_at) === 0) {
            if ($last_payment->state != StateLists::PAYMENT['PAID']) {
                return StateLists::PAYMENT['EXCEEDED'];
            }else{
                return StateLists::PAYMENT['PAID'];
            }
        }

        return StateLists::PAYMENT['PENDING'];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
