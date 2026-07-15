<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminServices\ProgressStatisticsService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\Controller;

class ProgressStatisticsController extends Controller
{
    private ProgressStatisticsService $progressStatisticsService;

    public function __construct(ProgressStatisticsService  $progressStatisticsService){
        $this->progressStatisticsService = $progressStatisticsService;
    }

    public function progressStatisticsOverview(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->progressStatisticsService->progressStatisticsOverview();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}