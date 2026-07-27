<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\ChapterContent;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class ChapterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = Excel::toArray([], storage_path('app/import/chapter_contents.xlsx'));

        // أول Sheet
        $rows = $rows[0];

        // حذف أول سطر (العناوين)
        unset($rows[0]);

        foreach ($rows as $row) {

           if (empty($row[0])) {
        continue;
    }
    $chapterName = trim($row[0]);

$pdf = trim($row[1] ?? '');
$audio = trim($row[2] ?? '');
$video = trim($row[3] ?? '');

$pdfProgress = (int) ($row[4] ?? 0);
$audioProgress = (int) ($row[5] ?? 0);
$videoProgress = (int) ($row[6] ?? 0);

    $chapter = Chapter::where('title', $chapterName)->first();

    if (!$chapter) {
        $this->command?->warn("Chapter not found: {$chapterName}");
        continue;
    }

    $contents = [

        [
            'type' => 'pdf',
            'url' => $pdf,
            'progress' => $pdfProgress,
        ],

        [
            'type' => 'audio',
            'url' => $audio,
            'progress' => $audioProgress,
        ],

        [
            'type' => 'video',
            'url' => $video,
            'progress' => $videoProgress,
        ],
    ];

    foreach ($contents as $content) {

        ChapterContent::updateOrCreate(
            [
                'chapter_id' => $chapter->id,
                'type' => $content['type'],
            ],
            [
                'url' => $content['url'],
                'total_progress_value' => $content['progress'],
            ]
        );
    }

    $this->command?->info("Imported {$chapterName}");
}

    }
}


