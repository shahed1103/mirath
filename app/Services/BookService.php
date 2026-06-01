<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Classification;
use App\Http\Resources\BookResource;
use Exception;
use Throwable;

class BookService {

    public function getClassificationDetails($classificationId): array{
        $classification = Classification::with([
            'books' => fn ($query)
                    => $query 
                    ->select('id','classification_id','title','author_name','photo','level_id')
                    ->orderBy('level_id')
        ])->find($classificationId);

        $data = [
            'bio' => $classification->bio,
            'books' => BookResource::collection($classification->books),
        ];
        $message = 'Classification Details data retrieved successfully';
        return ['books' => $data , 'message' => $message];
    }
}

//     return [
//         'bio' => $classification->bio,
//         'books' => $classification->books->map(fn ($book) => [
//             'title' => $book->title,
//             'author_name' => $book->{'Author name'},
//             'photo' => $book->photo,
//         ]),
//     ];
// }