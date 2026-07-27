<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles =
        [
            'مدخل إلى علم الفقه',
            'سوية المؤمن',
            'منهج الاستدلال',
            'متن البناء في الصرف',
            'مدخل إلى فقه الحديث',
            'مصادر التلقي',
            'السيرة النبوية للمصلحين',
            'متن المعين'
        ];

        $authors =
        [
            'عامر بهجت',
            'أحمد السيد',
            'عيد الله العجيري',
            'سالم قحطاني',
            'تركي الغميز',
            'أحمد السيد',
            'أحمد السيد',
            'أحمد السيد'
        ];

        $bio =
        [
            'شرعي',
            'سلوكي',
            'فكري',
            'شرعي',
            'شرعي',
            'فكري',
            'إصلاحي',
            'شرعي'
        ];

        // أسماء الصور الموجودة داخل Bucket/covers
        $photos = [
            'covers/1.jpg',
            'covers/2.jpg',
            'covers/3.jpg',
            'covers/4.jpg',
            'covers/5.jpg',
            'covers/6.jpg',
            'covers/7.jpg',
            'covers/8.jpg',
        ];

        $classifications = [2,1,4,2,2,4,3,2];
        $levels = [1,2,3,2,3,1,1,2];

        $total_pages = [154,147,101,84,71,189,672,59];

        for ($i = 0; $i < 8; $i++) {

            Book::create([
                'title' => $titles[$i],
                'author_name' => $authors[$i],
                'bio' => $bio[$i],
                'photo' => $photos[$i],
                'classification_id' => $classifications[$i],
                'level_id' => $levels[$i],
                'total_pages' => $total_pages[$i],
            ]);
        }
    }
}