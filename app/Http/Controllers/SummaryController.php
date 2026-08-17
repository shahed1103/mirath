<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;
use App\Services\SummaryService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Summary\CreateSummaryRequest;
use App\Http\Requests\Summary\EditSummaryRequest;
use App\Http\Requests\Summary\UploadSummaryRequest;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class SummaryController extends Controller
{
    private SummaryService $summaryService;

    public function __construct(SummaryService  $summaryService){
        $this->summaryService = $summaryService;
    }

    public function addSummary(CreateSummaryRequest $request , $chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->addSummary($request , $chapterId);
            return Response::Success($data['summary'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }


    public function uploadSummary(UploadSummaryRequest $request , $chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->uploadSummary($request , $chapterId);
            return Response::Success($data['summary'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function editSummary(EditSummaryRequest $request , $summaryId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->editSummary($request , $summaryId);
            return Response::Success($data['summary'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function summaryDetails($summaryId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->summaryDetails($summaryId);
            return Response::Success($data['summary'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function deleteSummary($summaryId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->deleteSummary($summaryId);
            return Response::Success($data['summary'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function allCreatedSummary(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->allCreatedSummary();
            return Response::Success($data['summaries'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function allUploadedSummary(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->summaryService->allUploadedSummary();
            return Response::Success($data['summaries'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}
