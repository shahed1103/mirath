<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\StudyPlan;
use App\Models\StudyTask;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Data\BookProgress;

class TaskGeneratorService
{
public function resolveStartDate(array $studyDays): Carbon
{
    sort($studyDays);

    $date = today()->copy();

    while (!in_array($date->dayOfWeek, $studyDays)) {
        $date->addDay();
    }

    return $date;
}

private function nextStudyDate(Carbon $currentDate,array $studyDays): Carbon {

    $date = $currentDate->copy()->addDay();

    while (!in_array($date->dayOfWeek, $studyDays)) {
        $date->addDay();
    }

    return $date;
}

private function calculateBookShares(Collection $books, int $dailyPages): array {

    $totalPages = $books->sum('total_pages');

    $shares = [];

    foreach ($books as $book) {

        $pages = max(
            1,
            (int) round(
                ($book->total_pages / $totalPages)
                * $dailyPages
            )
        );

        $shares[$book->id] = $pages;

    }

    return $shares;
}



public function generate(StudyPlan $studyPlan): Carbon
{
    $books = $studyPlan
        ->selectedBooks()
        ->with([
            'chapters' => fn ($query) =>
                $query->orderBy('order_number')
        ])
        ->get();

    $studyDays = $studyPlan
        ->days()
        ->orderBy('day_number')
        ->pluck('day_number')
        ->toArray();

    if ($books->isEmpty()) {
        throw new \InvalidArgumentException(
            'لا توجد كتب في الخطة.'
        );
    }

    if (empty($studyDays)) {
        throw new \InvalidArgumentException(
            'لا توجد أيام دراسة في الخطة.'
        );
    }

    /*
     * حساب نصيب كل كتاب من الورد اليومي
     */
    $shares = $this->calculateBookShares(
        $books,
        $studyPlan->daily_pages
    );

    /*
     * إنشاء حالة تقدم مستقلة لكل كتاب
     */
    $progress = [];

    foreach ($books as $book) {

        if ($book->chapters->isEmpty()) {
            throw new \InvalidArgumentException(
                "الكتاب {$book->title} لا يحتوي على فصول."
            );
        }

        $firstChapter = $book->chapters->first();

        $progress[$book->id] = new BookProgress(
            $firstChapter->start_page
        );
    }

    /*
     * أول يوم دراسة
     */
    $currentDate = $studyPlan->start_date->copy();

    /*
     * آخر يوم تم إنشاء مهمة فيه
     */
    $lastTaskDate = null;

    /*
     * ترتيب المهام
     */
    $readingOrder = 1;

    /*
     * نستمر طالما يوجد كتاب لم ينتهِ
     */
    while (
        $this->hasRemainingBooks(
            $books,
            $progress
        )
    ) {

        /*
         * توليد مهام هذا اليوم لكل الكتب
         */
        foreach ($books as $book) {

            $bookProgress = $progress[$book->id];

            /*
             * إذا انتهى الكتاب نتجاوزه
             */
            if ($bookProgress->finished) {
                continue;
            }

            $this->generateBookTasks(
                $studyPlan,
                $book,
                $bookProgress,
                $shares[$book->id],
                $currentDate,
                $readingOrder
            );

        }

        /*
         * هذا آخر يوم تم إنشاء مهام فيه
         */
        $lastTaskDate = $currentDate->copy();

        /*
         * إذا بقيت كتب، ننتقل إلى يوم الدراسة التالي
         */
        if (
            $this->hasRemainingBooks(
                $books,
                $progress
            )
        ) {

            $currentDate = $this->nextStudyDate(
                $currentDate,
                $studyDays
            );
        }
    }

    return $lastTaskDate;
}

private function hasRemainingBooks(
    Collection $books,
    array $progress
): bool {

    foreach ($books as $book) {

        if (!$progress[$book->id]->finished) {
            return true;
        }

    }

    return false;
}

private function generateBookTasks(
    StudyPlan $studyPlan,
    Book $book,
    BookProgress $progress,
    int $pagesForToday,
    Carbon $taskDate,
    int &$readingOrder
): void {

    $remainingPagesToday = $pagesForToday;

    while ($remainingPagesToday > 0 && !$progress->finished) {

        $chapter = $book->chapters[$progress->chapterIndex];

        $chapterRemaining =
            $chapter->end_page
            - $progress->currentPage
            + 1;

        $pagesToRead = min(
            $remainingPagesToday,
            $chapterRemaining
        );

        $fromPage = $progress->currentPage;

$toPage = $fromPage + $pagesToRead - 1;

$this->createTask(
    $studyPlan,
    $book,
    $chapter,
    $taskDate,
    $fromPage,
    $toPage,
    $readingOrder
);

$readingOrder++;

$remainingPagesToday -= $pagesToRead;

$progress->currentPage = $toPage + 1;

        /*
         * انتهى الفصل؟
         */
        if ($progress->currentPage > $chapter->end_page) {

            $progress->chapterIndex++;

            /*
             * انتهى الكتاب؟
             */
            if (
                $progress->chapterIndex >=
                $book->chapters->count()
            ) {

                $progress->finish();

                break;
            }

            $nextChapter =
                $book->chapters[$progress->chapterIndex];

            $progress->currentPage =
                $nextChapter->start_page;

        }

    }

}

   private function createTask(
    StudyPlan $studyPlan,
    Book $book,
    Chapter $chapter,
    Carbon $date,
    int $fromPage,
    int $toPage,
    int $readingOrder
): void {


    StudyTask::create([

        'study_plan_id' => $studyPlan->id,

        'user_id' => $studyPlan->user_id,

        'book_id' => $book->id,

        'chapter_id' => $chapter->id,

        'task_date' => $date,

        'from_page' => $fromPage,

        'to_page' => $toPage,

        'pages' => abs($toPage - $fromPage) + 1,

        'completed' => false,

        'reading_order' => $readingOrder

    ]);

}

}
