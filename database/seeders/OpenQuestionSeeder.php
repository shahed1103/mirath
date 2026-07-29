<?php

namespace Database\Seeders;

use App\Models\OpenQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class OpenQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $files = File::files(database_path('seeders/data/open_questions'));

        foreach ($files as $file) {

            $data = json_decode(File::get($file), true);

            if (
                !$data ||
                !isset($data['chapter_id']) ||
                !isset($data['questions'])
            ) {
                continue;
            }

            foreach ($data['questions'] as $question) {

                OpenQuestion::create([
                    'chapter_id'    => $data['chapter_id'],
                    'question_text' => $question['question_text'],
                    'answer'        => $question['answer'],
                    'order_number'  => $question['order_number'],
                ]);
            }
        }
    }
}