<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title',
        'room_id',
        'description',
        'created_by',
        'started_at',
        'ended_at',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

