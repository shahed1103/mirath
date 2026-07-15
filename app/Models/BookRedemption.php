<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookRedemption extends Model
{

    protected $fillable = [
    'user_id',
    'library_book_id',
     'points_spent',
     'status'

    ];

    public function book()
{
    return $this->belongsTo(LibraryBook::class, 'library_book_id');
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
