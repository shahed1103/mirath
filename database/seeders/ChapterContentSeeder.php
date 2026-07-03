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
        $file_pdf = url(Storage::url('uploads/booksphotos/التلقي.pdf'));
        $file_youtubeUrl = 'https://youtu.be/7JsCnDKc3Sk?si=u7jSqUSmId5AMbx4';
        $file_voiceMp3 = url(Storage::url('uploads/booksphotos/التلقي.mp3'));


        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'pdf',
                'url' => $file_pdf,
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
                'url' => $file_voiceMp3,
                'total_progress_value' => rand(300, 2400),
            ]);
        }
    }
}
