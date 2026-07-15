<?php

namespace Database\Seeders;

use App\Models\LibraryBook;
use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class LibraryBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photos = [
            'health(1).jpg',
            'health(2).jpg',
            'health(3).jpg',
            'health(4).jpg'
        ];

        $sourceDir = public_path('uploads/seeder_photos/');
        $targetDir = 'uploads/booksphotos/';

        foreach ($photos as $photo) {
            $sourcePath = $sourceDir . $photo;
            $targetPath = $targetDir . $photo;

            if (File::exists($sourcePath)) {
                Storage::disk('public')->put($targetPath, File::get($sourcePath));

                $fullPath = $targetPath;
                // $fullPath = url(Storage::url($targetPath));

                $fullPaths[] = $fullPath;
            } else {
                $fullPaths[] = null;
            }
        }

        $books = [
            [
                'name' => 'رياض الصالحين',
                'author' => 'الإمام النووي',
                'price' => 250,
                'count' => 10,
                'photo' => $fullPaths[1] ,
            ],
            [
                'name' => 'الأربعون النووية',
                'author' => 'الإمام النووي',
                'price' => 150,
                 'count' => 10,
                 'photo' => $fullPaths[1] ,
            ],
            [
                'name' => 'العقيدة الواسطية',
                'author' => 'ابن تيمية',
                'price' => 300,
                 'count' => 10,
                 'photo' => $fullPaths[2] ,
            ],
            [
                'name' => 'مختصر صحيح البخاري',
                'author' => 'الإمام الزبيدي',
                'price' => 400,
                 'count' => 10,
                 'photo' => $fullPaths[2] ,
            ],
            [
                'name' => 'فقه السيرة',
                'author' => 'محمد الغزالي',
                'price' => 280,
                 'count' => 10,
                 'photo' => $fullPaths[3] ,
            ],
            [
                'name' => 'الرحيق المختوم',
                'author' => 'صفي الرحمن المباركفوري',
                'price' => 350,
                 'count' => 10,
                 'photo' => $fullPaths[3] ,
            ],
            [
                'name' => 'تفسير السعدي',
                'author' => 'عبد الرحمن السعدي',
                'price' => 500,
                 'count' => 10,
                 'photo' => $fullPaths[3] ,
            ],
            [
                'name' => 'الوابل الصيب',
                'author' => 'ابن القيم',
                'price' => 220,
                 'count' => 10,
                 'photo' => $fullPaths[1] ,
            ],
            [
                'name' => 'حصن المسلم',
                'author' => 'سعيد بن وهف القحطاني',
                'price' => 120,
                 'count' => 10,
                 'photo' => $fullPaths[2] ,
            ],
            [
                'name' => 'مدارج السالكين',
                'author' => 'ابن القيم',
                'price' => 600,
                 'count' => 10,
                 'photo' => $fullPaths[1] ,
            ],
        ];

        foreach ($books as $book) {
            LibraryBook::create($book);
        }
    }
}
