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

        if ($data['plan_type'] === 'duration') {

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
        int $dailyPages,
        int $totalBooks,
    ): array {

        $dailyPages = (int) ceil(
            $totalPages / $targetDays
        );

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

    private function calculateByDailyPages(
        int $totalPages,
        int $dailyPages,
        int $totalBooks,
        int $targetDays
    ): array {

        $targetDays = (int) ceil(
            $totalPages / $dailyPages
        );

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
}
