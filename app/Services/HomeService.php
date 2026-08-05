<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Classification;
use App\Models\Feature;
use App\Models\Chapter;
use App\Models\ContentProgress;
use Illuminate\Support\Facades\Cache;
use Exception;
use Throwable;
use Storage;

class HomeService {

    public function getClassifications(): array{
        $classifications = Cache::remember('view_classifications' , 3600, function (){
            return Classification::select('id' , 'classification')
                ->get()
                ->map(function ($classification) {
                    return [
                        'id' => $classification->id,
                        'classification' => $classification->classification, 
                    ];
                })
                ->toArray();
        });
        return $classifications;
    }

    public function getFeatures(): array{
        $features = Cache::remember('view_features' , 3600, function (){
            return Feature::select('id' , 'feature')
                ->get()
                ->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'feature' => $feature->feature, 
                    ];
                })
                ->toArray();
        });
        return $features;
    }

    public function getContinueReading(): array{
        $progress = ContentProgress::with([
            'content:id,type,url,chapter_id',
            'content.chapter:id,title,book_id',
            'content.chapter.book:id,title,classification_id,total_pages,photo,author_name',
            'content.chapter.book.classification:id,classification'
            ])
            ->where('user_id', auth()->id())
            ->latest('last_accessed_at')
            ->first();

        if (!$progress) {
               return [
                'data' => null,
                'message' => 'No active reading'
            ];
        }

        $data = [    
            'book_name' => $progress->content?->chapter?->book?->title,
            'book_photo' => url(Storage::url($progress->content?->chapter?->book?->photo)),
            'author_name' => $progress->content?->chapter?->book?->author_name,
            'classification' => $progress->content?->chapter?->book?->classification?->classification,
            'chapter_title' => $progress->content?->chapter?->title,
            'chapter_id' => $progress->content?->chapter?->id,
        ];
        return $data;
    }
    
    public function updateProgress($request , $contentId): array {
        $progress = ContentProgress::updateOrCreate(

            [
                'user_id' => auth()->id(),
                'content_id' => $contentId,
            ],

            [
                'progress' => $request->progress,
                'last_accessed_at' => now(),
            ]
        );

        return [
            'data' => null,
            'message' => 'progress saved successfully',
        ];
    }

    public function addFeedback($request): array {
        $user = User::find(auth()->id());
        if (!$user) {
            throw new Exception('User not found');
        }

        $user->feedbacks()->create([
            'feedback' => $request->feedback,
        ]);

        return [
            'data' => null,
            'message' => 'Feedback submitted successfully',
        ];
    }
}

