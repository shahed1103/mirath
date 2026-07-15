<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
        protected $fillable = [
        'name',
        'author',
        'price',
        'count',
        'photo'

    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'library_book_id');
    }

    public function redemptions()
    {
        return $this->hasMany(BookRedemption::class, 'library_book_id');
    }
}
