<?php

namespace App\Services\AdminServices;
use App\Models\User;
use App\Models\Book;
use App\Models\Chapter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContentStatisticsService {
    private function totalBooksNumber(): int{
        return Book::count();
    }

    private function totalChaptersNumber(): int{
        return Chapter::count();
    }

    private function topFiveBooks(): int{
        return Chapter::count();
    }

    private function leastFiveBooks(): int{
        return Chapter::count();
    }

    public function contentStatisticsOverview(): array{

        $data = [
            'total_books_number' => $this->totalBooksNumber(),

            'total_chapters_number' => $this->totalChaptersNumber(),

            'top_five_books' => $this->topFiveBooks(),

            'least_five_books' => $this->leastFiveBooks(),
        ];

        return [
            'data' => $data,
            'message' => 'content statices retrived successfully'
        ];
    }
}
