<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Book;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Classification;
use App\Models\ContentProgress;
use App\Models\UserChapterProgress;
use App\Http\Resources\BookResource;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\BookDetailsResource;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Storage;

class BookService {

    public function getClassificationDetails($classificationId): array{
        $classification = Classification::select('id','bio')
                            ->with(['books' => fn ($query)
                                            => $query 
                                            ->select('id','classification_id','title','author_name','photo','level_id')
                                            ->orderBy('level_id')
                            ])->findOrFail($classificationId);

        $data = [
                'bio' => $classification->bio,
                'books' => BookResource::collection($classification->books),
        ];
        $message = 'Classification details data retrieved successfully';
        return ['books' => $data , 'message' => $message];
    }

    public function getBookDetails($bookId): array{
        $userId = auth()->id();
        $book = Book::select('id','title','author_name','photo','total_pages','bio')
                     ->withCount('chapters')
                     ->with(['chapters:id,book_id,title,order_number',
                             'chapters.contents.progresses'=>
                                    function ($query) use ($userId) {
                                        $query->where('user_id', $userId)
                                              ->select('id','content_id','progress');
                                    },
                             'chapters.progress' => 
                                    function ($query) use ($userId) {
                                        $query->where('user_id', $userId)
                                            ->select( 'id', 'chapter_id', 'is_open'); 
                                    }
                            ])->findOrFail($bookId);

        $data = [
            'book' => new BookDetailsResource($book),
            'chapters' => ChapterResource::collection($book->chapters),
        ];
        $message = 'Book details data retrieved successfully';
        return ['chapters' => $data , 'message' => $message];
    }

    public function getChapterDetails($chapterId): array{ 
        $userId = auth()->id();
        $chapter = Chapter::with('contents' , 'summaries')
            ->findOrFail($chapterId);

        $progresses = ContentProgress::where('user_id', $userId)
            ->whereIn('content_id', $chapter->contents->pluck('id'))
            ->get()
            ->keyBy('content_id');

        if($chapter->order_number != 1){
            $isUnlocked = UserChapterProgress::where('user_id' , $userId)
                ->where('chapter_id' , $chapterId)
                ->where('is_open' , true)
                ->exists();

            if(!$isUnlocked){
                throw new Exception('Chapter is locked' , 403);
            }
        }
            $contents = [
                'pdf' => null,
                'audio' => null,
                'video' => null,
            ];

        foreach ($chapter->contents as $content) {

            $progress = $progresses[$content->id]->progress ?? 0;

            $contents[$content->type] = [
                'id' => $content->id,
                'url' => ($content->type === 'video') ? $content->url : url(Storage::url($content->url)),
                'progress' => $progress
            ];
        }

        // use the loaded relation to check if the user already has a summary for this chapter
        $summaryChapterAlready = $chapter->summaries->contains(function ($summary) use ($userId) {
            return $summary->user_id == $userId;
        });

        $data = [
            'chapter_title' => $chapter->title ?? null,
            'have_summary' => $summaryChapterAlready ?? false,
            'pdf'           => $contents['pdf'],
            'audio'         => $contents['audio'],
            'video'         => $contents['video'],
        ];

        $message = 'Chapter contents data retrieved successfully';
        return ['contents' => $data , 'message' => $message];
    }
}