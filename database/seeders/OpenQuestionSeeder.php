<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OpenQuestion;

class OpenQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        for ($chapterId = 1; $chapterId <= 6; $chapterId++) {

            for ($order = 1; $order <= 5; $order++) {

                $data[] = [
                    'chapter_id' => $chapterId,
                    'question_text' => "سؤال مفتوح رقم {$order} للباب رقم {$chapterId}",
                    'answer' => "هذه إجابة السؤال رقم {$order} في الباب رقم {$chapterId}. يمكن استبدال هذا النص بالإجابة الحقيقية لاحقاً.",
                    'order_number' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        OpenQuestion::insert($data);
    }
}