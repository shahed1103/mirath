<?php

namespace App\Services;

use App\Models\Book;
use InvalidArgumentException;

class PlanCalculatorService
{
     public function calculate(array $data): array
{
    $books = Book::whereIn('id', $data['book_ids'])->get();

    $totalPages = $books->sum('total_pages');
    $totalBooks = $books->count();

    if ($totalPages == 0) {
        throw new InvalidArgumentException('Books have no pages.');
    }

    if ($data['plan_type'] == 'duration') {

        return $this->calculateByDuration(
            $totalPages,
            $data['target_days'],
            $totalBooks
        );

    }

    return $this->calculateByDailyPages(
        $totalPages,
        $data['daily_pages'],
        $totalBooks
    );
}




private function response(
    int $totalBooks,
    int $totalPages,
    int $dailyPages,
    int $targetDays
): array {

    return [

        'data' => [

            'total_books' => $totalBooks,

            'total_pages' => $totalPages,

            'daily_pages' => $dailyPages,

            'target_days' => $targetDays,

        ],

        'message' => 'تم حساب الخطة بنجاح.'

    ];
}



private function calculateByDuration(
    int $totalPages,
    int $targetDays,
    int $totalBooks
): array {

    $dailyPages = (int) ceil($totalPages / $targetDays);

    return $this->response(
        $totalBooks,
        $totalPages,
        $dailyPages,
        $targetDays
    );
}



private function calculateByDailyPages(
    int $totalPages,
    int $dailyPages,
    int $totalBooks
): array {

    $targetDays = (int) ceil($totalPages / $dailyPages);

    return $this->response(
        $totalBooks,
        $totalPages,
        $dailyPages,
        $targetDays
    );
}

}
