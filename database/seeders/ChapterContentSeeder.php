<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\ChapterContent;
use Illuminate\Support\Facades\Storage;

class ChapterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'مصادر التلقي والمعرفة.pdf';
        $file = url('storage/uploads/det/مصادر التلقي والمعرفة.pdf');

        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'pdf',
                'url' => $file,
                'total_progress_value' => rand(50, 300),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'video',
                'url' => $file,
                'total_progress_value' => rand(300, 3600),
            ]);

            ChapterContent::create([
                'chapter_id' => $chapter->id,
                'type' => 'audio',
                'url' => $file,
                'total_progress_value' => rand(300, 2400),
            ]);
        }
    }
}
