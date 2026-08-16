<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlanReminderLog extends Model
{
    protected $fillable = [
        'study_plan_id',
        'user_id',
        'reminder_date',
        'sent_at',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'sent_at' => 'datetime',
    ];
}