<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResultDetail extends Model
{
    protected $fillable = [
        'exam_result_id', 'question_id', 'answer_id', 'essay_answer', 'is_correct'
    ];


    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
