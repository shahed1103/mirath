<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = [

            // الفصل الأول - نجاح من أول مرة (24/25 = 96%)
            [
                'user_id' => 4,
                'chapter_id' => 1,
                'questions_answered' => 20,
                'correct_answers' => 19,
                'estimated_duration' => 18,
                'current_level_score' => 96,
                'success' => true,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(10),
                'finished_at' => Carbon::now()->subDays(10)->addMinutes(18),
                'points' => 3,
            ],

            // الفصل الثاني - رسوب
            [
                'user_id' => 5,
                'chapter_id' => 2,
                'questions_answered' => 20,
                'correct_answers' => 10,
                'estimated_duration' => 20,
                'current_level_score' => 760,
                'success' => false,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(8),
                'finished_at' => Carbon::now()->subDays(8)->addMinutes(20),
                'points' => 0,
            ],

            // الفصل الثاني - نجاح بالمحاولة الثانية
            [
                'user_id' => 6,
                'chapter_id' => 2,
                'questions_answered' => 20,
                'correct_answers' => 18,
                'estimated_duration' => 17,
                'current_level_score' => 292,
                'success' => true,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(7),
                'finished_at' => Carbon::now()->subDays(7)->addMinutes(17),
                'points' => 0,
            ],

            // الفصل الثالث - نجاح من أول مرة
            [
                'user_id' => 7,
                'chapter_id' => 3,
                'questions_answered' => 20,
                'correct_answers' => 20,
                'estimated_duration' => 15,
                'current_level_score' => 100,
                'success' => true,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(5),
                'finished_at' => Carbon::now()->subDays(5)->addMinutes(15),
                'points' => 3,
            ],

            // الفصل الرابع - امتحان جارٍ
            [
                'user_id' => 8,
                'chapter_id' => 4,
                'questions_answered' => 8,
                'correct_answers' => 7,
                'estimated_duration' => 10,
                'current_level_score' => 280,
                'success' => false,
                'status' => 'active',
                'started_at' => Carbon::now()->subMinutes(10),
                'finished_at' => null,
                'points' => 0,
            ],

        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }
    }
}
