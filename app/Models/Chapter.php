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
        'order_number'
    ];

    
    public function book(): BelongsTo{
        return $this->belongsTo(Book::class);
    }

    public function contents(): HasMany {
        return $this->hasMany(ChapterContent::class);
    }

    public function usersInReviewList(){
        return $this->belongsToMany(User::class,'chapter_user');
    }

    public function questions(): HasMany {
        return $this->hasMany(Question::class);
    }

    public function exams(): HasMany {
    return $this->hasMany(Exam::class);
    }

    public function progress(): HasMany {
        return $this->hasMany(UserChapterProgress::class);
    }
    
    // public function questionHistory(): HasMany {
    //     return $this->hasMany(UserQuestionHistory::class);
    // }
}
