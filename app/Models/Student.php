<?php

namespace App\Models;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * The roles that belong to the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)->using(GroupStudent::class)->withPivot('certificate');
    }
}
