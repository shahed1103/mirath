<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserQusetionHistory extends Model
{    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'question_id',
        'appeared_count',
        'last_seen_at',
        'answered_correctly'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo {
        return $this->belongsTo(Chapter::class);
    }

    public function question(): BelongsTo {
        return $this->belongsTo(Question::class);
    }
}
