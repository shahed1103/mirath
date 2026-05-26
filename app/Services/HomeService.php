<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Classification;
use App\Models\Feature;
use App\Models\Chapter;
use App\Models\ReadingProgress;
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

        // return [
        //     'classifications' => $classifications,
        //     'message' => 'all classifications are retrieved successfully'
        // ];
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

        // return [
        //     'features' => $features,
        //     'message' => 'all features are retrieved successfully'
        // ];
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

        // return ['data' => $data , 'message' => 'book to continue reading is retrived successfully'];
        return $data;
    }

    public function updateReadingProgress($request): array {
        $progress = ReadingProgress::updateOrCreate(

            [
                'user_id' => auth()->id(),
                'book_id' => $request->book_id,
            ],

            [
                'current_page' => $request->current_page,
                'current_chapter' => $request->current_chapter,
                'last_read_at' => now(),
            ]
        );

        return [
            'data' => null,
            'message' => 'Reading progress saved successfully',
        ];
    }
}