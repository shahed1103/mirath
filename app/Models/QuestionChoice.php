<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
    ];

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function examQuestions(){
        return $this->hasMany(
            ExamQuestion::class,
            'selected_choice_id'
        );
    }
}
