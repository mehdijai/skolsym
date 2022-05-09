<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

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
