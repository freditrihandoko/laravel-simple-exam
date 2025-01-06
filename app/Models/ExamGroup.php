<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamGroup extends Pivot
{
    protected $table = 'exam_groups';

    protected $fillable = [
        'exam_id', 'group_id'
    ];
}
