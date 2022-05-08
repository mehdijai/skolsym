<?php

namespace App\Models;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)->using(GroupStudent::class)->withPivot('certificate');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id', 'id');
    }
}
