<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'questions_answered',
        'correct_answers',
        'estimated_duration',
        'current_level_score',
        'status',
        'started_at',
        'finished_at',
        'success'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    
    public function chapter(): BelongsTo{
        return $this->belongsTo(Chapter::class);
    }
}
