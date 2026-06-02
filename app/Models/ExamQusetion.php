<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQusetion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_id',
        'order_number',
        'answered',
        'selected_choice_id',
        'is_correct',
        'earned_score',
        'answered_at',
    ];

    public function exam(): BelongsTo {
    return $this->belongsTo(Exam::class);
    }

    public function question(): BelongsTo {
        return $this->belongsTo(Question::class);
    }

    public function selectedChoice(): BelongsTo {
        return $this->belongsTo(
            QuestionChoice::class,
            'selected_choice_id'
        );
    }
}
