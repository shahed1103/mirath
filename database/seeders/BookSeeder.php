<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = ['مدخل إلى علم الفقه',
        'سوية المؤمن' ,
        'منهج الاستدلال ',
        'متن البناء في الصرف  ',
        'مدخل إلى فقه الحديث ' ,
        'مصادر التلقي' ,
        'السيرة النبوية للمصلحين' ,
        'متن المعين'];

        $authors = ['عامر بهجت'  ,
        'أحمد السيد' ,
        'عيد الله العجيري' ,
        'سالم قحطاني',
        'تركي الغميز',
        'أحمد السيد',
        'أحمد السيد',
        'أحمد السيد'];

        $bio = ['شرعي' ,
        'سلوكي' ,
        'فكري' ,
        'شرعي',
        'شرعي',
        'فكري',
        'إصلاحي',
        'شرعي'];

        $photos = [
            'health(1).jpg',
            'health(2).jpg',
            'health(3).jpg',
            'health(4).jpg',
            'health(4).jpg',
            'health(4).jpg',
            'health(4).jpg',
            'health(4).jpg',

        ];

        $classifications = [2,1,4,2,2,4,3,2];
        $levels = [1,2,3,2,3,1,1,4 ];

        $total_pages = [183,127,105,30 ,74 ,196 ,675 , 51];
        $fullPaths = [];

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

        for ($i=0; $i < 8 ; $i++) {
            Book::query()->create([
           'title' => $titles[$i] ,
           'author_name' => $authors[$i] ,
           'bio' => $bio[$i] ,
           'photo' => $fullPaths[$i] ,
           'classification_id'=> $classifications[$i],
           'level_id'=> $levels[$i],
           'total_pages' => $total_pages[$i]
            ]); }
    }
}
