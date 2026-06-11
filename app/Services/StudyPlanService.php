<?php

namespace App\Services;

use App\Models\Book;
use App\Models\StudyPlan;
use App\Models\StudyPlanDay;
use App\Models\StudyTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudyPlanService
{
    public function createStudyPlan(array $data): array
    {
        $plan = null;

        DB::transaction(function () use ($data, &$plan) {

            $plan = StudyPlan::create([
                'user_id' => Auth::id(),
                'type' => $data['type'],
                'daily_chapters' => $data['daily_chapters'] ?? null,
                'duration_days' => $data['duration_days'] ?? null,
                'notification_time' => $data['notification_time'],
                'is_offline' => $data['is_offline'] ?? false,
            ]);

            $plan->books()->attach($data['book_ids']);

            foreach ($data['study_days'] as $day) {
                StudyPlanDay::create([
                    'study_plan_id' => $plan->id,
                    'day_of_week' => $day,
                ]);
            }

            $this->generateTasks(
                $plan,
                $data['book_ids'],
                $data['study_days']
            );
        });

        return [
            'plan' => $plan,
            'message' => 'Study plan created successfully'
        ];
    }

    private function generateTasks(
        StudyPlan $plan,
        array $bookIds,
        array $studyDays
    ): void {

        $books = Book::with([
            'chapters' => fn ($q) => $q->orderBy('order_number')
        ])
        ->whereIn('id', $bookIds)
        ->get();

        $chaptersByBook = [];

        foreach ($books as $book) {
            $chaptersByBook[$book->id] =
                $book->chapters->values()->all();
        }

        $totalChapters = collect($chaptersByBook)
            ->flatten()
            ->count();

        if ($plan->type === 'fixed_duration') {

            $dailyChapters = max(
                1,
                (int) ceil(
                    $totalChapters / $plan->duration_days
                )
            );

        } else {

            $dailyChapters = $plan->daily_chapters;
        }

        $currentDate = $this->getNextStudyDate(
            Carbon::tomorrow(),
            $studyDays
        );

        $bookQueue = array_keys($chaptersByBook);

        while (true) {

            $tasksForDay = [];

            for ($i = 0; $i < $dailyChapters; $i++) {

                if (empty($bookQueue)) {
                    break;
                }

                $bookId = array_shift($bookQueue);

                if (empty($chaptersByBook[$bookId])) {
                    $i--;
                    continue;
                }

                $chapter = array_shift(
                    $chaptersByBook[$bookId]
                );

                $tasksForDay[] = [
                    'user_id' => Auth::id(),
                    'study_plan_id' => $plan->id,
                    'book_id' => $bookId,
                    'chapter_id' => $chapter->id,
                    'task_date' => $currentDate->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (!empty($chaptersByBook[$bookId])) {
                    $bookQueue[] = $bookId;
                }
            }

            if (empty($tasksForDay)) {
                break;
            }

            StudyTask::insert($tasksForDay);

            $remaining = collect($chaptersByBook)
                ->flatten()
                ->count();

            if ($remaining === 0) {
                break;
            }

            $currentDate = $this->getNextStudyDate(
                $currentDate->copy()->addDay(),
                $studyDays
            );
        }
    }

    private function getNextStudyDate(
        Carbon $date,
        array $studyDays
    ): Carbon {

        while (
            !in_array(
                strtolower($date->englishDayOfWeek),
                $studyDays
            )
        ) {
            $date->addDay();
        }

        return $date;
    }
}
