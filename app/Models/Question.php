<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'question_text',
        'explanation',
        'difficulty_score',
        'estimated_time'
    ];

    public function chapter(): BelongsTo{
        return $this->belongsTo(Chapter::class);
    }

    public function choices(): HasMany {
        return $this->hasMany(QuestionChoice::class);
    }

    public function history(): HasMany {
        return $this->hasMany(UserQuestionHistory::class);
    }

    public function examQuestions(): HasMany {
        return $this->hasMany(ExamQuestion::class);
    }
}
