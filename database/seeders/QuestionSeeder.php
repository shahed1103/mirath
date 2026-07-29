<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $files = File::files(database_path('seeders/data/mcq_questions'));

        foreach ($files as $file) {

            $data = json_decode(File::get($file), true);

            if (
                !$data ||
                !isset($data['chapter_id']) ||
                !isset($data['questions'])
            ) {
                continue;
            }

            DB::transaction(function () use ($data) {

                foreach ($data['questions'] as $item) {

                    $question = Question::create([
                        'chapter_id'       => $data['chapter_id'],
                        'question_text'    => $item['question_text'],
                        'difficulty_score' => $item['difficulty_score'],
                        'explanation'      => $item['explanation'],
                    ]);
                    

                    foreach ($item['choices'] as $choice) {

                        QuestionChoice::create([
                            'question_id' => $question->id,
                            'choice_text' => $choice['choice_text'],
                            'is_correct'  => $choice['is_correct'],
                        ]);
                    }
                }

            });
        }
    }
}