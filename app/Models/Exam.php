<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'started_at',
        'finished_at',
        'total_score',
        'passed',
        'total_time',
        'status',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    
    public function chapter(): BelongsTo{
        return $this->belongsTo(Chapter::class);
    }

    public function examQuestions(): HasMany{
        return $this->hasMany(ExamQuestion::class);
    }
}
