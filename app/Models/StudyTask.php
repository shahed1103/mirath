<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

  class StudyTask extends Model
{
    protected $fillable = [
        'user_id',
        'study_plan_id',
        'book_id',
        'chapter_id',
        'task_date',
        'status',
        'completed_at'
    ];

    public function plan()
    {
        return $this->belongsTo(StudyPlan::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
