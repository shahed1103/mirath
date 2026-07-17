<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookDetailsResource;
use Illuminate\Validation\ValidationException;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Classification;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\OpenQuestion;
use App\Models\Feedback;
use App\Models\Level;
use Exception;
use Throwable;
use Illuminate\Support\Facades\Cache;
use Storage;

class ContentAdminService {

    private const CONTENT_TYPE_PDF = 'pdf';
    private const CONTENT_TYPE_AUDIO = 'audio';
    private const CONTENT_TYPE_VIDEO = 'video';
    
    private const CONTENT_TYPES = [
        self::CONTENT_TYPE_PDF,
        self::CONTENT_TYPE_AUDIO,
        self::CONTENT_TYPE_VIDEO,
    ];
    
    public function getBookDetailsAdmin($bookId): array{
        $book = Book::select('id','title','author_name','photo','total_pages','bio' , 'level_id')
                     ->withCount('chapters')
                     ->with(['chapters:id,book_id,title,order_number' , 'level:id,level'])
                     ->findOrFail($bookId);
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
                'url' => ($content->type === 'video') ? $content->url : url(Storage::url($content->url)),
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

    public function addNewClassification($request): array{
        return DB::transaction(function () use ($request) {
            $classification = Classification::create([
                'classification' => $request->classification,
                'bio' => $request->classification_bio,
            ]);

            $book = $this->createBook(
                $request,
                $classification->id
            );

            $chapter = $this->createChapter($book->id,$request->chapter_title,1,$request->start_page,$request->end_page);

            $chapterContents = $this->createChapterContents($chapter->id, $request);

            Cache::forget('view_classifications');

            return [
                'message' => 'New classification, book, chapter, and contents added successfully.',
                'data' => [
                    'classification' => $classification,
                    'book' => $book,
                    'chapter' => $chapter,
                    'chapter_contents' => $chapterContents,
                ],
            ];
        });
    }

    public function addNewBook($request , $classificationId): array{
        return DB::transaction(function () use ($request, $classificationId) {
            $classification = Classification::findOrFail($classificationId);

            $book = $this->createBook(
                $request,
                $classification->id
            );

            $chapter = $this->createChapter($book->id,$request->chapter_title,1,$request->start_page,$request->end_page);

            $chapterContents = $this->createChapterContents($chapter->id, $request);

            return [
                'message' => 'New book added successfully.',
                'data' => [
                    'book' => $book,
                    'chapter' => $chapter,
                    'chapter_contents' => $chapterContents,
                ], 
            ];
        });
    }

    public function addNewChapter($request , $bookId): array{
        return DB::transaction(function () use ($request, $bookId) {
            $book = Book::findOrFail($bookId);

            $nextOrderNumber = Chapter::where('book_id', $book->id)
            ->max('order_number');

            $nextOrderNumber = $nextOrderNumber ? $nextOrderNumber + 1 : 1;

            $chapter = $this->createChapter($book->id,$request->chapter_title,$nextOrderNumber,$request->start_page,$request->end_page);

            $chapterContents = $this->createChapterContents($chapter->id, $request);

            return [
                'message' => 'New chapter added successfully.',
                'data' => [
                    'chapter' => $chapter,
                    'chapter_contents' => $chapterContents,
                ],
            ];
        });
    }

    private function createChapterContents(int $chapterId, $request): array{
        $chapterContents = [];

        foreach (self::CONTENT_TYPES as $type) {

            $urlField = $type . '_url';

            if ($request->hasFile($urlField)) {
                $file = $request->file($urlField);
                $path = $file->store('uploads/chapter_contents', 'public');
            }
            
            $chapterContents[$type] = ChapterContent::create([
                'chapter_id' => $chapterId,
                'type' => $type,
                'url' => $path ?? $request->$urlField ?? null,
                'total_progress_value' => $request->total_progress_value,
            ]);
            $path = null; 
        }

        return $chapterContents;
    }

    private function createBook($request, int $classificationId): Book {
        if ($request->hasFile('photo')) {
             $photo = $request->file('photo');
             $path = $photo->store('uploads/booksphotos', 'public');
        }

        return Book::create([
            'title' => $request->title,
            'author_name' => $request->author_name,
            'photo' => $path ?? null,
            'total_pages' => $request->total_pages,
            'bio' => $request->bio,
            'level_id' => $request->level_id,
            'classification_id' => $classificationId,
        ]);
    }

    private function createChapter($bookId,$title,$orderNumber,$startPage,$endPage): Chapter{
        return Chapter::create([
            'book_id' => $bookId,
            'title' => $title,
            'start_page' => $startPage,
            'end_page' => $endPage,
            'order_number' => $orderNumber,
        ]);
    }

    public function editClassification($request , $classificationId): array{
        $classification = Classification::findOrFail($classificationId);

        $classification->update([
            'classification' => $request->classification ?? $classification->classification,
            'bio' => $request->bio ?? $classification->bio,
        ]);

        Cache::forget('view_classifications');

        return [
            'message' => 'Classification updated successfully.',
            'data' => $classification,
        ];
    }

    public function editBook($request , $bookId): array{
        $book = Book::findOrFail($bookId);

        $book->update([
            'title' => $request->title ?? $book->title,
            'author_name' => $request->author_name ?? $book->author_name,
            'photo' => $request->photo ?? $book->photo,
            'total_pages' => $request->total_pages ?? $book->total_pages,
            'bio' => $request->bio ?? $book->bio,
            'level_id' => $request->level_id ?? $book->level_id,
        ]);

        return [
            'message' => 'Book updated successfully.',
            'data' => $book,
        ];
    }

    public function editChapter($request , $chapterId): array{
        $chapter = Chapter::findOrFail($chapterId);

        $chapter->update([
            'title' => $request->chapter_title ?? $chapter->title,
            'start_page' => $request->start_page ?? $chapter->start_page,
            'end_page' => $request->end_page ?? $chapter->end_page
        ]);

        return [
            'message' => 'Chapter updated successfully.',
            'data' => $chapter,
        ];
    }

    public function editChapterContent($request , $contentId): array{
        $content = ChapterContent::findOrFail($contentId);

            if ($request->hasFile('url')) {
                $file = $request->file('url');
                $path = $file->store('uploads/chapter_contents', 'public');
            }
        $content->update([
            'type' => $request->type ?? $content->type,
            'url' => $path ?? $content->url,
        ]);

        return [
            'message' => 'Chapter content updated successfully.',
            'data' => $content,
        ];
    }
    
    public function deleteClassification($classificationId): array {
        $classification = Classification::findOrFail($classificationId);

        $classificationsCount = Classification::count();

        if ($classificationsCount <= 1) {
            throw new Exception ('At least one classification must remain in the system.' , 422);
        }

        $classification->delete();

        Cache::forget('view_classifications');

        return [
            'data' => $classification,
            'message' => 'Classification deleted successfully.',
        ];
    }

    public function deleteBook($bookId): array {
        $book = Book::findOrFail($bookId);

        $booksCount = Book::where('classification_id', $book->classification_id)
            ->count();

        if ($booksCount <= 1) {
            throw new Exception('At least one book must remain in this classification.', 422);
        }

        $book->delete();

        return [
            'data' => $book,
            'message' => 'Book deleted successfully.',
        ];
    }

    public function deleteChapter($chapterId): array {
        $chapter = Chapter::findOrFail($chapterId);

        $chaptersCount = Chapter::where('book_id', $chapter->book_id)
            ->count();

        if ($chaptersCount <= 1) {
            throw new Exception('At least one chapter must remain in this book.', 422);
        }

        $chapter->delete();

        return [
            'data' => $chapter,
            'message' => 'Chapter deleted successfully.',
        ];
    }
}