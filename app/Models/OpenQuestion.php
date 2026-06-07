<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenQuestion extends Model
{
    protected $fillable = [
        'chapter_id',
        'question_text',
        'answer',
        'order_number',
    ];

    public function chapter() {
        return $this->belongsTo(Chapter::class);
    }
}
