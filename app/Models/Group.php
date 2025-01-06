<?php

namespace App\Models;

use App\Traits\hasRolenGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;
    use hasRolenGroups;


    protected $fillable =
    [
        'name',
        'status',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_group');
    }

    public function scopeWithoutAdmin($query)
    {
        return $query->where('name', '!=', 'ADMIN');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_groups');
    }

    //untuk role dan groups ada di Traits/hasRolenGroups.php
}
