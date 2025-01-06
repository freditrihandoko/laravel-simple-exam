<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'exam_duration', 'exam_start', 'exam_end', 'shuffle_questions', 'shuffle_answers', 'show_score'
    ];

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'exam_topic')->withPivot('num_questions');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'exam_groups');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('topic_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
