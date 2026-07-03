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




public function createPlan(array $data): array
{
    $books = Book::whereIn('id', $data['book_ids'])->get();

    $totalPages = $books->sum('total_pages');
    $totalBooks = $books->count();

    $allowedDays = $data['study_days'];
    sort($allowedDays);

    /*
    -----------------------------------
    1. START DATE (first valid study day)
    -----------------------------------
    */
    $startDate = Carbon::today();

    while (!in_array($startDate->dayOfWeek, $allowedDays)) {
        $startDate->addDay();
    }

    /*
    -----------------------------------
    2. TOTAL DAYS
    -----------------------------------
    */
    if ($data['plan_type'] === 'duration') {
        $totalDays = $data['target_days'];
    } else {
        $totalDays = (int) ceil($totalPages / $data['daily_pages']);
    }

    $endDate = $startDate->copy();

    /*
    -----------------------------------
    3. CREATE PLAN
    -----------------------------------
    */
    $plan = StudyPlan::create([
        'user_id' => auth()->id(),
        'plan_type' => $data['plan_type'],
        'daily_pages' => $data['daily_pages'] ?? null,
        'target_days' => $data['target_days'] ?? null,
        'notification_time' => $data['notification_time'],
        'offline' => $data['offline'],
        'start_date' => $startDate,
        'end_date' => $startDate->copy()->addDays($totalDays),
        'status' => 'active',
        'total_books' => $totalBooks,
        'total_pages' => $totalPages,
    ]);

    /*
    -----------------------------------
    4. BOOK RELATION
    -----------------------------------
    */
    foreach ($books as $book) {
        StudyPlanBook::create([
            'study_plan_id' => $plan->id,
            'book_id' => $book->id
        ]);
    }

    /*
    -----------------------------------
    5. STUDY DAYS
    -----------------------------------
    */
    foreach ($allowedDays as $day) {
        StudyPlanDay::create([
            'study_plan_id' => $plan->id,
            'day_number' => $day
        ]);
    }

    /*
    -----------------------------------
    6. PREP: PAGE POINTERS
    -----------------------------------
    */
    $pagePointer = [];
    $bookWeights = [];

    foreach ($books as $book) {
        $pagePointer[$book->id] = 1;

        // weight = نسبة الكتاب من إجمالي الصفحات
        $bookWeights[$book->id] = $book->total_pages / $totalPages;
    }

    /*
    -----------------------------------
    7. TASK GENERATION (ADVANCED)
    -----------------------------------
    */
    $currentDate = $startDate->copy();

    $remainingPages = $totalPages;

    while ($remainingPages > 0) {

        if (in_array($currentDate->dayOfWeek, $allowedDays)) {

            $dailyPages = $data['plan_type'] === 'duration'
                ? (int) ceil($totalPages / $totalDays)
                : $data['daily_pages'];

            $remainingForDay = $dailyPages;

            foreach ($books as $book) {

                if ($remainingForDay <= 0) break;

                $bookRemaining = $book->total_pages - ($pagePointer[$book->id] - 1);

                if ($bookRemaining <= 0) continue;

                /*
                -----------------------------------
                WEIGHTED DISTRIBUTION (SMART PART)
                -----------------------------------
                */
                $weightShare = (int) ceil($dailyPages * $bookWeights[$book->id]);

                $take = min($weightShare, $bookRemaining, $remainingForDay);

                if ($take <= 0) continue;

                StudyTask::create([
                    'study_plan_id' => $plan->id,
                    'book_id' => $book->id,
                    'task_date' => $currentDate->toDateString(),
                    'from_page' => $pagePointer[$book->id],
                    'to_page' => $pagePointer[$book->id] + $take - 1,
                    'pages' => $take,
                ]);

                $pagePointer[$book->id] += $take;
                $remainingForDay -= $take;
                $remainingPages -= $take;
            }
        }

        $currentDate->addDay();
    }

    /*
    -----------------------------------
    8. RETURN
    -----------------------------------
    */
    return [
        'plan' => $plan,
        'message' => 'Study plan created successfully'
    ];
}
}
