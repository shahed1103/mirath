<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuestionChoice;

class QuestionChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $question_ids = [1,1,1,1,
                        2,2,2,2,
                        3,3,3,3,
                        4,4,4,4,
                        5,5,5,5,
                        6,6,6,6,
                        7,7,7,7,
                        8,8,8,8,
                        9,9,9,9,
                        10,10,10,10,
                        11,11,11,11,
                        12,12,12,12,
                        13,13,13,13,
                        14,14,14,14,
                        15,15,15,15,
                        16,16,16,16,
                        17,17,17,17,
                        18,18,18,18,
                        19,19,19,19,
                        20,20,20,20];
        $choice_text = ['التعامل مع المخالفين' , 'دلائل أصول الإسلام' , 'بوصلة المصلح' , 'مصادر التلقي والمعرفة' ];
        $is_correct = [false , false , false ,true];

        for ($i=0; $i < 60 ; $i++) {
            QuestionChoice::query()->create([
           'question_id'=> $question_ids[$i],
           'choice_text' => $choice_text[$i % count($choice_text)],
           'is_correct' => $is_correct[$i % count($is_correct)]
            ]); }
    }
}

