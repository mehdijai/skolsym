<?php

namespace App\Models;

use App\Const\RefGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->ref = RefGenerator::generate();
        });
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
