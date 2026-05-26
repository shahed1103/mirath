<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'book_id'
    ];

    
    public function book(): BelongsTo{
        return $this->belongsTo(Book::class);
    }
}
