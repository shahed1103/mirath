<?php

namespace App\Services;

use App\Models\Book;
use Carbon\Carbon;
use App\Models\StudyPlan;
use App\Models\StudyPlanBook;
use App\Models\StudyPlanDay;
use App\Models\StudyTask;

class StudyPlanService
{

public function calculatePlan(array $data): array
{
    $books = Book::whereIn('id', $data['book_ids'])->get();
    $totalPages = $books->sum('total_pages');
    $totalBooks = $books->count();
    if ($data['plan_type'] == 'duration') {
        $dailyPages = (int) ceil($totalPages / $data['target_days']);
        $targetDays = $data['target_days'];
    } else {
        $dailyPages = $data['daily_pages'];
        $targetDays = (int) ceil($totalPages / $dailyPages);
    }

    return [
        'plan' => [
            'total_books' => $totalBooks,
            'total_pages' => $totalPages,
            'daily_pages' => $dailyPages,
            'target_days' => $targetDays,
        ],
        'message' => 'Plan calculated successfully.',
    ];
}




private function getFirstStudyDate(array $studyDays): Carbon
{
    $date = Carbon::today();

    while (!in_array($date->dayOfWeek, $studyDays)) {
        $date->addDay();
    }

    return $date;
}


private function calculateEndDate(
    Carbon $startDate,
    array $studyDays,
    int $sessions
): Carbon {
    $date = $startDate->copy();
    $completedSessions = 1;
    while ($completedSessions < $sessions) {
        $date->addDay();
        if (in_array($date->dayOfWeek, $studyDays)) {
            $completedSessions++;
        }
    }
    return $date;
}


private function buildReadingQueue(Collection $books): array
{
    $queue = [];
    foreach ($books as $book) {
        foreach ($book->chapters->sortBy('order_number') as $chapter) {
            $queue[$book->id][] = [
                'chapter_id' => $chapter->id,
                'chapter_title' => $chapter->title,
                'start_page' => $chapter->start_page,
                'end_page' => $chapter->end_page,
                // الصفحة الحالية داخل الفصل
                'current_page' => $chapter->start_page,
                // عدد الصفحات المتبقية داخل الفصل
                'remaining_pages' =>
                    ($chapter->end_page - $chapter->start_page) + 1

            ];
        }
    }
    return $queue;
}


}
