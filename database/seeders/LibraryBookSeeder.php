<?php

namespace Database\Seeders;

use App\Models\LibraryBook;
use Illuminate\Database\Seeder;

class LibraryBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'name' => 'رياض الصالحين',
                'author' => 'الإمام النووي',
                'price' => 250,
                'count' => 10,
                'photo' => 'covers/9.png',
            ],
            [
                'name' => 'الأربعون النووية',
                'author' => 'الإمام النووي',
                'price' => 150,
                'count' => 10,
                'photo' => 'covers/10.png',
            ],
            [
                'name' => 'العقيدة الواسطية',
                'author' => 'ابن تيمية',
                'price' => 300,
                'count' => 10,
                'photo' => 'covers/11.png',
            ],
            [
                'name' => 'مختصر صحيح البخاري',
                'author' => 'الإمام الزبيدي',
                'price' => 400,
                'count' => 10,
                'photo' => 'covers/12.png',
            ],
            [
                'name' => 'فقه السيرة',
                'author' => 'محمد الغزالي',
                'price' => 280,
                'count' => 10,
                'photo' => 'covers/13.png',
            ],
            [
                'name' => 'الرحيق المختوم',
                'author' => 'صفي الرحمن المباركفوري',
                'price' => 350,
                'count' => 10,
                'photo' => 'covers/14.png',
            ],
            [
                'name' => 'تفسير السعدي',
                'author' => 'عبد الرحمن السعدي',
                'price' => 500,
                'count' => 10,
                'photo' => 'covers/15.png',
            ],
            [
                'name' => 'الوابل الصيب',
                'author' => 'ابن القيم',
                'price' => 220,
                'count' => 10,
                'photo' => 'covers/16.png',
            ],
            [
                'name' => 'حصن المسلم',
                'author' => 'سعيد بن وهف القحطاني',
                'price' => 120,
                'count' => 10,
                'photo' => 'covers/17.png',
            ],
            [
                'name' => 'مدارج السالكين',
                'author' => 'ابن القيم',
                'price' => 600,
                'count' => 10,
                'photo' => 'covers/18.png',
            ],
        ];

        foreach ($books as $book) {
            LibraryBook::create($book);
        }
    }
}
