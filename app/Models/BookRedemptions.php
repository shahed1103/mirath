<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookRedemptions extends Model
{

    protected $fillable = [
    'user_id',
    'library_book_id',
   'points_spent',

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
