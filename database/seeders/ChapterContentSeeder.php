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
        $pdfPath = 'chapters/0MGHB3kH2eEwWAmVaDzom8448iJrDkDLQ6WYJ17V.pdf';
        $audioPath = 'audios/1Cfs3GcBBUSn2KKXEk5KtShZeBgvLxSTn5Epi6vJ.mp3';
        $youtubeUrl = 'https://youtu.be/7JsCnDKc3Sk?si=u7jSqUSmId5AMbx4';

        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'pdf',
                'url' =>  $pdfPath,
                'total_progress_value' => rand(50, 300),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'video',
                'url' => $youtubeUrl,
                'total_progress_value' => rand(300, 3600),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'audio',
                'url' => $audioPath,
                'total_progress_value' => rand(300, 2400),
            ]);
        }
    }
}
