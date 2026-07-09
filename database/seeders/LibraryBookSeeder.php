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
            ],
            [
                'name' => 'الأربعون النووية',
                'author' => 'الإمام النووي',
                'price' => 150,
                 'count' => 10,
            ],
            [
                'name' => 'العقيدة الواسطية',
                'author' => 'ابن تيمية',
                'price' => 300,
                 'count' => 10,
            ],
            [
                'name' => 'مختصر صحيح البخاري',
                'author' => 'الإمام الزبيدي',
                'price' => 400,
                 'count' => 10,
            ],
            [
                'name' => 'فقه السيرة',
                'author' => 'محمد الغزالي',
                'price' => 280,
                 'count' => 10,
            ],
            [
                'name' => 'الرحيق المختوم',
                'author' => 'صفي الرحمن المباركفوري',
                'price' => 350,
                 'count' => 10,
            ],
            [
                'name' => 'تفسير السعدي',
                'author' => 'عبد الرحمن السعدي',
                'price' => 500,
                 'count' => 10,
            ],
            [
                'name' => 'الوابل الصيب',
                'author' => 'ابن القيم',
                'price' => 220,
                 'count' => 10,
            ],
            [
                'name' => 'حصن المسلم',
                'author' => 'سعيد بن وهف القحطاني',
                'price' => 120,
                 'count' => 10,
            ],
            [
                'name' => 'مدارج السالكين',
                'author' => 'ابن القيم',
                'price' => 600,
                 'count' => 10,
            ],
        ];

        foreach ($books as $book) {
            LibraryBook::create($book);
        }
    }
}
