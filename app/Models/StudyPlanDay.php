<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyPlanDay extends Model
{
    protected $fillable = [
        'study_plan_id',
        'day_of_week'
    ];

    public function plan()
    {
        return $this->belongsTo(StudyPlan::class);
    }
}
