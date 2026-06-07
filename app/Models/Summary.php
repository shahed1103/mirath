<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'title',
        'content',
        'edited',
        'edited_at',
        'summaryCreated'
    ];

    protected $casts = [
    'edited_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }
}
