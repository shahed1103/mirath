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

        // if (!Storage::disk('public')->exists($path)) {
        //     dd('File not found');
        // }

        $file = url('storage/uploads/det/مصادر التلقي والمعرفة.pdf');

        $types = ['pdf', 'video', 'audio'];
        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            foreach ($types as $type) {

                ChapterContent::create([
                    'chapter_id' => $chapter->id,
                    'type' => $type,
                    'url' => $file
                    // storage_path('app/public/السيرة الذاتية.pdf'),
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
