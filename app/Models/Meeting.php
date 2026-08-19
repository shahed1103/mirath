<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        // 'title',
        // 'room_id',
        // 'description',
        // 'created_by',
        // 'started_at',
        // 'ended_at',

        'id',
'title',
'description',
'meeting_link',
'room_name',
'type',
'scheduled_date',
'scheduled_time',
'created_by',
'reminder_sent_at'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

