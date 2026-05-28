<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chapter;
use App\Models\ChapterContent;

class ChapterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['pdf', 'video', 'audio'];
        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            foreach ($types as $type) {

                ChapterContent::create([
                    'chapter_id' => $chapter->id,
                    'type' => $type,
                    'url' => $this->generateFakeUrl($type, $chapter->id),
                ]);

            }
        }
    }

    private function generateFakeUrl($type, $chapterId) {
        return match ($type) {
            'pdf' => "https://example.com/pdf/chapter_$chapterId.pdf",
            'video' => "https://example.com/video/chapter_$chapterId.mp4",
            'audio' => "https://example.com/audio/chapter_$chapterId.mp3",
        };
    }
}
