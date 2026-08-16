<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Services\StudyPlanService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudyPlanReminderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error('لا يوجد مستخدم في قاعدة البيانات.');
            return;
        }

        $book = Book::first();

        if (!$book) {
            $this->command->error('لا يوجد كتاب في قاعدة البيانات.');
            return;
        }

        $notificationTime = Carbon::now()->format('H:i');

        $today = Carbon::now()->dayOfWeek;

        $service = app(StudyPlanService::class);

        $result = $service->create(
            $user->id,
            [
                'book_ids' => [
                    $book->id
                ],

                'plan_type' => 'daily_pages',

                'daily_pages' => 10,

                'target_days' => null,

                'study_days' => [
                    $today
                ],

                'notification_time' => $notificationTime,

                'offline' => false,
            ]
        );

        $studyPlan = $result['data'];
    }
}