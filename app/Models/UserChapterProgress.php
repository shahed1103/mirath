<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserChapterProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'is_open'
        // 'is_unlocked',
        // 'is_completed',
        // 'current_score',
        // 'current_level_score',
        // 'attempts_count'
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo {
        return $this->belongsTo(Chapter::class);
    }
}


