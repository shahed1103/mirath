<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\BookDetailsResource;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Classification;

use App\Models\Feedback;
use Exception;
use Throwable;

class AdminService {

    private const CONTENT_TYPE_PDF = 'pdf';
    private const CONTENT_TYPE_AUDIO = 'audio';
    private const CONTENT_TYPE_VIDEO = 'video';
    
    private const CONTENT_TYPES = [
        self::CONTENT_TYPE_PDF,
        self::CONTENT_TYPE_AUDIO,
        self::CONTENT_TYPE_VIDEO,
    ];

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

            $chapter = $this->createChapter($book->id,$request->chapter_title,1);

            $chapterContents = $this->createChapterContents($chapter->id, $request);

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

            $chapter = $this->createChapter($book->id,$request->chapter_title,1);

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

            $chapter = $this->createChapter($book->id,$request->chapter_title,$request->order_number);

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

    private function createChapter($bookId,$title,$orderNumber): Chapter{
        return Chapter::create([
            'book_id' => $bookId,
            'title' => $title,
            'order_number' => $orderNumber,
        ]);
    }

    public function editClassification($request , $classificationId): array{
        $classification = Classification::findOrFail($classificationId);

        $classification->update([
            'classification' => $request->classification ?? $classification->classification,
            'bio' => $request->bio ?? $classification->bio,
        ]);

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
            'order_number' => $request->order_number ?? $chapter->order_number,
        ]);

        return [
            'message' => 'Chapter updated successfully.',
            'data' => $chapter,
        ];
    }

    public function editChapterContent($request , $contentId): array{
        $content = ChapterContent::findOrFail($contentId);

        $content->update([
            'type' => $request->type ?? $content->type,
            'url' => $request->url ?? $content->url,
        ]);

        return [
            'message' => 'Chapter content updated successfully.',
            'data' => $content,
        ];
    }
    
    public function deleteClassification($classificationId): array{
            $classification = Classification::findOrFail($classificationId);
            $classification->delete();

            return [
                'message' => 'Classification deleted successfully.',
            ];
    }

    public function deleteBook($bookId): array{
            $book = Book::findOrFail($bookId);
            $book->delete();

            return [
                'message' => 'Book deleted successfully.',
            ];
    }

    public function deleteChapter($chapterId): array{
            $chapter = Chapter::findOrFail($chapterId);
            $chapter->delete();

            return [
                'message' => 'Chapter deleted successfully.',
            ];
    }

    public function allChapterQuestionsWithAnswers($chapterId): array{
        $chapter = Chapter::with('questions.choices')->findOrFail($chapterId);

        $questionsWithAnswers = $chapter->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'answers' => $question->choices->map(function ($choice) {
                    return [
                        'id' => $choice->id,
                        'answer_text' => $choice->choice_text,
                        'is_correct' => $choice->is_correct,
                    ];
                }),
            ];
        });

        return [
            'data' => $questionsWithAnswers,
            'message' => 'All questions with answers retrieved successfully.',
        ];
    }

    public function allChapterOpenQuestionsWithAnswers($chapterId): array{
        $chapter = Chapter::with('openQuestions')->findOrFail($chapterId);

        $openQuestionsWithAnswers = $chapter->openQuestions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'answer' => $question->answer,
            ];
        });

        return [
            'data' => $openQuestionsWithAnswers,
            'message' => 'All open questions with answers retrieved successfully.',
        ];
    }

    public function addQuestionToChapter($request , $chapterId): array{
        $chapter = Chapter::findOrFail($chapterId);

        $question = DB::transaction(function () use ($chapter, $request) {
            $question = Question::create([
                'chapter_id' => $chapter->id,
                'question_text' => $request->question_text,
            ]);

            foreach ($request->answers as $answer) {
                QuestionChoice::create([
                    'question_id' => $question->id,
                    'choice_text' => $answer['answer_text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            return $question;
        });

        return [
            'message' => 'Question and answers added successfully.',
            'data' => [
                'question' => $question,
                'answers' => $question->choices,
            ],
        ];
    }

    public function addOpenQuestionToChapter($request , $chapterId): array{
        $chapter = Chapter::findOrFail($chapterId);

        $openQuestion = OpenQuestion::create([
            'chapter_id' => $chapter->id,
            'question_text' => $request->question_text,
            'answer' => $request->answer,
        ]);

        return [
            'message' => 'Open question added successfully.',
            'data' => $openQuestion,
        ];
    }

    public function editQuestion($request , $questionId): array{
        $question = Question::findOrFail($questionId);

        $question->update([
                'question_text' => $request->question_text ?? $question->question_text,
            ]);

        $question->refresh();

        return [
            'message' => 'Question updated successfully.',
            'data' => [
                'question' => $question,
            ],
        ];
    }

    public function editChoice($request , $choiceId): array{
        $choice = QuestionChoice::findOrFail($choiceId);

        $choice->update([
            'choice_text' => $request->choice_text ?? $choice->choice_text,
            'is_correct' => $request->is_correct ?? $choice->is_correct,
        ]);

        return [
            'message' => 'Choice updated successfully.',
            'data' => $choice,
        ];
    }

    public function editOpenQuestion($request , $openQuestionId): array{
        $openQuestion = OpenQuestion::findOrFail($openQuestionId);

        $openQuestion->update([
            'question_text' => $request->question_text ?? $openQuestion->question_text,
            'answer' => $request->answer ?? $openQuestion->answer,
        ]);

        return [
            'message' => 'Open question updated successfully.',
            'data' => $openQuestion,
        ];
    }

    public function deleteQuestion($questionId): array{
            $question = Question::findOrFail($questionId);
            $question->delete();

        return [
            'message' => 'Question and its choices deleted successfully.',
        ];
    }

    public function deleteChoice($choiceId): array{
        $choice = QuestionChoice::findOrFail($choiceId);
        $choice->delete();

        return [
            'message' => 'Choice deleted successfully.',
        ];
    }   

    public function deleteOpenQuestion($openQuestionId): array{
        $openQuestion = OpenQuestion::findOrFail($openQuestionId);
        $openQuestion->delete();

        return [
            'message' => 'Open question deleted successfully.',
        ];
    }
}