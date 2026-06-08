<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Summary;
use App\Models\User;
use App\Models\Chapter;
use App\Http\Resources\SummaryResource;
use App\Http\Resources\SummaryUploadedResource;
use Illuminate\Support\Facades\Storage;
use Exception;
use Throwable;

class SummaryService {

    public function addSummary($request , $chapterId): array {
        $userId = auth()->id();
        $summaryChapterAlready = Summary::where('user_id' ,  $userId)
                                    ->where('chapter_id' , $chapterId)
                                    ->exists();

        if($summaryChapterAlready){
            throw new Exception ('you have summary for this chapter already', 422);
        }

        $summary = Summary::create([
        'user_id' =>  $userId,
        'chapter_id' => $chapterId,
        'title' => $request->title,
        'content' => $request->content,
        'summaryCreated' => true
        ]);

        return ['summary' => $request->title , 'message' => 'summary created successfully'];
    }

    public function uploadSummary($request , $chapterId): array {
        $userId = auth()->id();

        $summaryChapterAlready = Summary::where('user_id' ,  $userId)
                                    ->where('chapter_id' , $chapterId)
                                    ->exists();

        if($summaryChapterAlready){
            throw new Exception ('you have summary for this chapter already', 422);
        }
        
        $uploadedFile = $request->file('file');

        $path = $uploadedFile->storeAs(
                'uploads/summaries',
                $uploadedFile->getClientOriginalName(), 
                'public'
        );

        $url = asset('storage/' . $path);

        // Use original filename (without extension) as the summary title
        $originalName = $uploadedFile->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $title = trim(preg_replace('/[_-]+/', ' ', $filename));

        $summary = Summary::create([
        'user_id' =>  $userId,
        'chapter_id' => $chapterId,
        'title' => $title,
        'content' => $url,
        'summaryCreated' => false
        ]);

        return ['summary' => $summary->title , 'message' => 'summary upload successfully'];
    }

    public function editSummary($request , $summaryId): array {
        $summary = Summary::findOrFail($summaryId);

        if(!$summary->summaryCreated){
            throw new Exception('you can not edit this pdf file' , 404);
        }

        $edited = $request->title || $request->content ;

        $summary->update([
            'title' => $request->title ?? $summary->title ,
            'content' => $request->content ?? $summary->content,
            'edited' => $edited,
        ]);

        if($edited){
            $summary->edited_at = now();
        }

        $summary->save();
        return ['summary' => ' ' , 'message' => 'summary edited successfully'];
    }

    public function summaryDetails($summaryId): array {
        $summary = Summary::select('title' , 'content')->findOrFail($summaryId);
        return ['summary' => $summary , 'message' => 'summary details retrieved successfully'];
    }

    public function deleteSummary($summaryId): array {
        Summary::findOrFail($summaryId)->delete();
        return ['summary' => '' , 'message' => 'summary deleted successfully'];
    }

    public function allCreatedSummary(): array {
       $userId = auth()->id();

       $summaries = Summary::where('user_id' , $userId)
                        ->where('summaryCreated' , true)
                        ->get();

       return ['summaries' => SummaryResource::collection($summaries) , 'message' => 'all created summaries'];
    }

    public function allUploadedSummary(): array {
          $userId = auth()->id();

          $summaries = Summary::where('user_id' , $userId)
                                ->where('summaryCreated' , false)
                                ->get();

         return ['summaries' => SummaryUploadedResource::collection($summaries) , 'message' => 'all uploaded summaries'];
     }






}