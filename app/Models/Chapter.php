<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'book_id',
        'status_id'
    ];

    
    public function book(): BelongsTo{
        return $this->belongsTo(Book::class);
    }

    public function status(): BelongsTo{
        return $this->belongsTo(Status::class);
    }

    public function contents(): HasMany {
        return $this->hasMany(ChapterContent::class);
    }

    public function usersInReviewList(){
        return $this->belongsToMany(User::class,'chapter_user');
    }
}
