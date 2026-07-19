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
        $choice_text = ['التعامل مع المخالفين' , 'دلائل أصول الإسلام' , 'بوصلة المصلح' , 'مصادر التلقي والمعرفة' ];
        $is_correct = [false , false , false ,true];

        for ($i=1; $i <= 80 ; $i++) {
            for($j=0; $j < 4 ; $j++) {
                QuestionChoice::query()->create([
                    'question_id'=> $i,
                    'choice_text' => $choice_text[$j % count($choice_text)],
                    'is_correct' => $is_correct[$j % count($is_correct)]
                ]); 
            }
        }
    }
}

