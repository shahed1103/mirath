<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminServices\ContentStatisticsService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\Controller;

class ContentStatisticsController extends Controller
{
    private ContentStatisticsService $contentStatisticsService;

    public function __construct(ContentStatisticsService  $contentStatisticsService){
        $this->contentStatisticsService = $contentStatisticsService;
    }

    public function contentStatisticsOverview(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->contentStatisticsService->contentStatisticsOverview();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}