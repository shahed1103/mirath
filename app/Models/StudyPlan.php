<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'daily_chapters',
        'duration_days',
        'notification_time',
        'is_offline',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(
            Book::class,
            'study_plan_books'
        );
    }

    public function days()
    {
        return $this->hasMany(StudyPlanDay::class);
    }

    public function tasks()
    {
        return $this->hasMany(StudyTask::class);
    }
}
