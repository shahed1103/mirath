<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminServices\ExamStatisticsService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\Controller;

class ExamStatisticsController extends Controller
{
    private ExamStatisticsService $examStatisticsService;

    public function __construct(ExamStatisticsService  $examStatisticsService){
        $this->examStatisticsService = $examStatisticsService;
    }

    public function examStatisticsOverview(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->examStatisticsService->examStatisticsOverview();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}