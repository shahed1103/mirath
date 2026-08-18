<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;
//use Maatwebsite\Excel\Facades\Excel;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
$rows = array_map('str_getcsv', file(storage_path('app/import/chapter_contents.csv')));
// $rows = array_map(
//     'str_getcsv',
//     file(database_path('seeders/data/chapter_contents.csv'))
// );
        // حذف العناوين
        unset($rows[0]);

        foreach ($rows as $row) {

            if (empty($row[0])) {
                continue;
            }

            Chapter::updateOrCreate(

                [
                    'book_id' => (int) ($row[7] ?? 0),
                    'order_number' => (int) ($row[8] ?? 0),
                ],

                [
                    'title' => trim($row[0]),
                    'start_page' => (int) ($row[9] ?? 0),
                    'end_page' => (int) ($row[10] ?? 0),
                ]
            );

            $this->command?->info('Imported: '.trim($row[0]));
        }

        $this->command?->info('Chapter Seeder Finished Successfully.');
    }
}
