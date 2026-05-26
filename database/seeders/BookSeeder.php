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
        $titles = ['التعامل مع المخالفين' , 'دلائل أصول الإسلام' , 'بوصلة المصلح' , 'مصادر التلقي والمعرفة'];
        $bio = ['سلوكي' , 'شرعي' , 'إصلاحي' , 'فكري'];
        $photos = [
            'health (1).jpg',
            'health (2).jpg',
            'health (3).jpg',
            'health (4).jpg'
        ];
        $classifications = [1,2,3,4];
        $total_pages = [90,100,150,200];
        $fullPaths = [];

        $sourceDir = public_path('uploads/seeder_photos/');
        $targetDir = 'uploads/booksphotos/';

        foreach ($photos as $photo) {
            $sourcePath = $sourceDir . $photo;
            $targetPath = $targetDir . $photo;

            if (File::exists($sourcePath)) {
                Storage::disk('public')->put($targetPath, File::get($sourcePath));

                // $fullPath = url(Storage::url($targetPath));
                $fullPath =  $targetPath;

                $fullPaths[] = $fullPath;
            } else {
                $fullPaths[] = null;
            }
        }

        for ($i=0; $i < 4 ; $i++) {
            Book::query()->create([
           'title' => $titles[$i] ,
           'bio' => $bio[$i] ,
           'photo' => $fullPaths[$i] ,
           'classification_id'=> $classifications[$i], 
           'total_pages' => $total_pages[$i]
            ]); }
    }
}
