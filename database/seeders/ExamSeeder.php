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
                'user_id' => 1,
                'chapter_id' => 1,
                'questions_answered' => 25,
                'correct_answers' => 24,
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
                'user_id' => 3,
                'chapter_id' => 2,
                'questions_answered' => 25,
                'correct_answers' => 15,
                'estimated_duration' => 20,
                'current_level_score' => 60,
                'success' => false,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(8),
                'finished_at' => Carbon::now()->subDays(8)->addMinutes(20),
                'points' => 0,
            ],

            // الفصل الثاني - نجاح بالمحاولة الثانية
            [
                'user_id' => 3,
                'chapter_id' => 2,
                'questions_answered' => 25,
                'correct_answers' => 23,
                'estimated_duration' => 17,
                'current_level_score' => 92,
                'success' => true,
                'status' => 'finished',
                'started_at' => Carbon::now()->subDays(7),
                'finished_at' => Carbon::now()->subDays(7)->addMinutes(17),
                'points' => 0,
            ],

            // الفصل الثالث - نجاح من أول مرة
            [
                'user_id' => 3,
                'chapter_id' => 3,
                'questions_answered' => 25,
                'correct_answers' => 25,
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
                'user_id' => 3,
                'chapter_id' => 4,
                'questions_answered' => 8,
                'correct_answers' => 7,
                'estimated_duration' => 10,
                'current_level_score' => 28,
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
