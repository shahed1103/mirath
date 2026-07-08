<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\ChapterContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
class ChapterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file_pdf = 'uploads/booksphotos/التلقي.pdf';
        $file_youtubeUrl = 'https://youtu.be/7JsCnDKc3Sk?si=u7jSqUSmId5AMbx4';
        $file_voiceMp3 = 'uploads/booksphotos/التلقي.mp3';

        $files = [
            'التلقي.pdf',
            'التلقي.mp3',
        ];

        $sourceDir = public_path('uploads/seeder_contents/');
        $targetDir = 'uploads/chapter_contents/';

        foreach ($files as $file) {
            $sourcePath = $sourceDir . $file;
            $targetPath = $targetDir . $file;
                Storage::disk('public')->put($targetPath, File::get($sourcePath));

                $fullPath = $targetPath;
                // $fullPath = url(Storage::url($targetPath));

                $fullPaths[] = $fullPath;
        }


        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'pdf',
                'url' => $fullPaths[0],
                'total_progress_value' => rand(50, 300),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'video',
                'url' => $file_youtubeUrl,
                'total_progress_value' => rand(300, 3600),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'audio',
                'url' => $fullPaths[1],
                'total_progress_value' => rand(300, 2400),
            ]);
        }
    }
}
