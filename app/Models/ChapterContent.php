<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterContent extends Model
{
    protected $fillable = [
        'chapter_id',
        'type',
        'url',
    ];

    public function chapter(): BelongsTo{
        return $this->belongsTo(Chapter::class);
    }

}
