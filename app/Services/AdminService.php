<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Resources\BookDetailsResource;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Feedback;
use Exception;
use Throwable;

class AdminService {

    public function getAllUsers(): array {
        $users = User::select('id', 'name', 'nick_name', 'email', 'age')
            ->role('Client')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return [
            'data' => $users,
            'message' => 'All users retrieved successfully.'
        ];
    }

    public function getAllFeedbacks(): array {
        $feedbacks = Feedback::with(['user:id,name,nick_name,email'])
            ->select('id', 'feedback', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($feedback) {
                return [
                    'id' => $feedback->id,
                    'feedback' => $feedback->feedback,
                    'created_at' => $feedback->created_at->format('Y-m-d'),
                    'user' => $feedback->user ? [
                        'user_id' => $feedback->user->id,
                        'name' => $feedback->user->name,
                        'nick_name' => $feedback->user->nick_name,
                        'email' => $feedback->user->email,
                    ] : null,
                ];
            })
            ->toArray();

        return [
            'data' => $feedbacks,
            'message' => 'All feedbacks retrieved successfully.'
        ];
    }

    public function getBookDetailsAdmin($bookId): array{
        $book = Book::select('id','title','author_name','photo','total_pages','bio' , 'level_id')
                     ->withCount('chapters')
                     ->with(['chapters:id,book_id,title,order_number' , 'level:id,level'])
                     ->findOrFail($bookId);
        // 'level_id',
        $chapters = $book->chapters->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'order_number' => $chapter->order_number,
            ];
        });

        // prepare book resource as array and include level name
        $bookResource = (new BookDetailsResource($book))->toArray(request());
        $bookResource['level_name'] = $book->level->level ?? null;

        $data = [
            'book' => $bookResource,
            'chapters' => $chapters,
        ];

        $message = 'Book details data retrieved successfully';
        return ['chapters' => $data, 'message' => $message];
    }

    public function getChapterDetailsAdmin($chapterId): array{ 
        $chapter = Chapter::with('contents')
            ->findOrFail($chapterId);

            $contents = [
                'pdf' => null,
                'audio' => null,
                'video' => null,
            ];

        foreach ($chapter->contents as $content) {
            $contents[$content->type] = [
                'id' => $content->id,
                'url' => $content->url,
            ];
        }

        $data = [
            'chapter_title' => $chapter->title ?? null,
            'pdf'           => $contents['pdf'],
            'audio'         => $contents['audio'],
            'video'         => $contents['video'],
        ];

        $message = 'Chapter contents data retrieved successfully';
        return ['contents' => $data , 'message' => $message];
    }

    public function addNew
}