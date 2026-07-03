<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_name',
        'bio',
        'photo',
        'classification_id',
        'level_id',
        'total_pages'
    ];

    public function chapters(): HasMany {
        return $this->hasMany(Chapter::class);
    }

    public function classification(): BelongsTo{
        return $this->belongsTo(Classification::class);
    }

    public function level(): BelongsTo{
        return $this->belongsTo(Level::class);
    }

public function studyPlanBooks(): HasMany
{
    return $this->hasMany(StudyPlanBook::class);
}

public function studyTasks(): HasMany
{
    return $this->hasMany(StudyTask::class);
}
}

