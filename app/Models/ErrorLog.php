<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'exception',
        'message',
        'status_code',
        'endpoint',
        'method',
        'ip',
            'file',
    'line',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
