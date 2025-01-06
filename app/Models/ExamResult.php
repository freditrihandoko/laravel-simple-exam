<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'score',
        'status',
        'start_time',
        'end_time',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ExamResultDetail::class);
    }
}
