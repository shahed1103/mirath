<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_type',
        'daily_pages',
        'target_days',
        'notification_time',
        'offline',
        'start_date',
        'end_date',
        'status',
       'total_pages',
        'total_books'
    ];

    protected $casts = [
        'offline' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'notification_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(StudyPlanBook::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(StudyPlanDay::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(StudyTask::class);
    }

    public function selectedBooks()
{
    return $this->belongsToMany(
        Book::class,
        'study_plan_books'
    );
}
}
