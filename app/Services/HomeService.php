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
        $progress = ReadingProgress::with(['book:id,title,classification_id,total_pages', 'book.classification:id,classification','chapter:id,title'])
            ->where('user_id', auth()->id())
            ->whereHas('book', function ($query) {
                $query->whereColumn(
                    'current_page',
                    '<',
                    'books.total_pages'
                );
            })
            ->latest('last_read_at')
            ->first();

        if (!$progress) {
               return [
                'data' => null,
                'message' => 'No active reading'
            ];
        }

        $data = [    
            'book_name' => $progress->book?->title,
            'classification' => $progress->book?->classification?->classification,
            'chapter_title' => $progress->chapter?->title,
        ];
        return $data;
    }

    public function openContinueReading(): array {
        $progress = ContentProgress::with([
            'content:id,type,url,chapter_id',
            'content.chapter:id,title'
        ])
            ->where('user_id', auth()->id())
            ->whereHas('content', function ($q) {
                $q->where('type', 'pdf');
            })
            ->latest('last_accessed_at')
            ->first();

        if (!$progress) {
            return [
                'data' => null,
                'message' => 'No active reading'
            ];

        }

        return [
            'data' => [
                'chapter_id' => $progress->content?->chapter_id,
                'chapter_title' => $progress->content->chapter?->title,
                'pdf_url' => $progress->content?->url,
                'current_page' => $progress->progress,
            ],
            'message' => 'Continue reading data retrieved successfully'
        ];
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
}

