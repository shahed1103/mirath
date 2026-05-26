<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'current_page',
        'current_chapter',
        'last_read_at'
    ];

    public function book(): BelongsTo{
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo{
        return $this->belongsTo(Chapter::class, 'current_chapter');
    }
}
