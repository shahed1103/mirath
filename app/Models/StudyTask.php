<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_id',
        'book_id',
        'chapter_id',
        'task_date',
        'from_page',
        'to_page',
        'pages',
        'completed',
        'completed_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'task_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
}
