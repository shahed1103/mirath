<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\User;
use App\Models\Chapter;
use App\Models\UserChapterProgress;
use Exception;
use App\Http\Resources\ReviewListResource;

class ChapterReviewService {

    public function addChapterToReviewList($chapterId): array{
        $user = auth()->user();

        $chapter = Chapter::findOrFail($chapterId);

        if($chapter->order_number != 1){
            $isUnLocked = UserChapterProgress::where('user_id' , $user->id)
                ->where('chapter_id' , $chapterId)
                ->where('is_unlocked' , true)
                ->exists();
            
            if(!$isUnLocked){
                throw new Exception('Chapter is locked' , 403);
            }
        }

        $reviewList = $user->reviewChapters()->syncWithoutDetaching([$chapterId]);
        $message = 'chapter add to review list successfully';
        return ['chapter' => null , 'message' => $message];
    }

    public function removeChapterFromReviewList($chapterId): array{
        $reviewList = auth()->user()->reviewChapters()->detach($chapterId);
        $message = 'chapter removed from review list successfully';
        return ['chapter' => null , 'message' => $message];
    }

    public function getReviewList(): array {
        $user = User::withCount('reviewChapters')
            ->with([
                'reviewChapters:id,book_id,title',
                'reviewChapters.book:id,title,classification_id',
                'reviewChapters.book.classification:id,classification'
            ])
            ->findOrFail(auth()->id());

        $data = [
            'total_chapters_num' => $user->review_chapters_count,
            'review_list' => ReviewListResource::collection($user->reviewChapters)
        ];

        return ['ReviewList' => $data,'message' => 'Review list retrieved successfully'];
    }
}