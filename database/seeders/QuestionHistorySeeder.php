<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserQuestionHistory;

class QuestionHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $user_ids = [1,2,3,4];
        $question_ids = [10,2,3,7,15,13,19,5,1,17];

        for ($i=0; $i < 10 ; $i++) {
            UserQuestionHistory::query()->create([
           'user_id' => 4 ,
           'question_id' => $question_ids[$i] ,
            ]); }
    }
}
