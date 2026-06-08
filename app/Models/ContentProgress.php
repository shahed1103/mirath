<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content_id', // could be book_id or chapter_id
        'progress', // percentage or page number
        'last_accessed_at', // timestamp of last access
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function content() : BelongsTo {
        return $this->belongsTo(ChapterContent::class);
    }

}


