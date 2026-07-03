<?php

namespace Database\Seeders;

use App\Models\CartItem;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cartItems = [
            [
                'user_id' => 1,
                'library_book_id' => 1,
            ],
            [
                'user_id' => 1,
                'library_book_id' => 2,
            ],
            [
                'user_id' => 2,
                'library_book_id' => 4,
            ],
            [
                'user_id' => 3,
                'library_book_id' => 6,
            ],
            [
                'user_id' => 3,
                'library_book_id' => 9,
            ],
        ];

        foreach ($cartItems as $item) {
            CartItem::create($item);
        }
    }
}
