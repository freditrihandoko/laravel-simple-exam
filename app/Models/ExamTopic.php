<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamTopic extends Pivot
{
    protected $table = 'exam_topic';

    protected $fillable = [
        'exam_id', 'topic_id', 'num_questions'
    ];
}
