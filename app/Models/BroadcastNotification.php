<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastNotification extends Model
{
    protected $fillable = [
    'title',
    'body',
    'type',
    'data'
    ];


    protected $casts = [
        'data' => 'array'
    ];
}
