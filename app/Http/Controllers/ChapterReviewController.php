<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;
use App\Services\ChapterReviewService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class ChapterReviewController extends Controller
{
    private ChapterReviewService $chapterReviewService;

    public function __construct(ChapterReviewService  $chapterReviewService){
        $this->chapterReviewService = $chapterReviewService;
    }

    public function addChapterToReviewList($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chapterReviewService->addChapterToReviewList($chapterId);
            return Response::Success($data['chapter'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function removeChapterFromReviewList($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chapterReviewService->removeChapterFromReviewList($chapterId);
            return Response::Success($data['chapter'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getReviewList(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->chapterReviewService->getReviewList();
            return Response::Success($data['ReviewList'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}
