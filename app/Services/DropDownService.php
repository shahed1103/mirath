<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Nationality;
use App\Models\Level;
use App\Models\Book;
use App\Models\Chapter;

use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;

class DropDownService {

    public function getNationalities(): array{
        $nationalities = Cache::remember('nationalities_dropdown', 3600, function () {
            return Nationality::select('id', 'nationality')
                ->get()
                ->map(function ($nationality) {
                    return [
                        'id' => $nationality->id,
                        'nationality' => $nationality->nationality,
                    ];
                })
                ->toArray();
        });

        return [
            'nationalities' => $nationalities,
            'message' => 'all nationalities are retrieved successfully',
        ];
    }

    public function getLevels(): array {
        
        $levels = Cache::remember('levels_dropdown', 3600, function () {
            return Level::select('id', 'level')
                ->get()
                ->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'level' => $level->level,
                    ];
                })
                ->toArray();
        });

        return [
            'levels' => $levels,
            'message' => 'all levels are retrieved successfully',
        ];
    }

    public function getBooks($classificationId): array {
        
        $books = Book::where('classification_id' , $classificationId)
                ->select('id', 'title')
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                    ];
                })
                ->toArray();

        return [
            'books' => $books,
            'message' => 'all books are retrieved successfully',
        ];
    }

    public function getChapters($bookId): array {
        
        $chapters = Chapter::where('book_id' , $bookId)
                ->select('id', 'title')
                ->get()
                ->map(function ($chapter) {
                    return [
                        'id' => $chapter->id,
                        'title' => $chapter->title,
                    ];
                })
                ->toArray();

        return [
            'chapters' => $chapters,
            'message' => 'all chapters are retrieved successfully',
        ];
    }
    
}