<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminServices\UserStatisticsService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\Controller;

class UserStatisticsController extends Controller
{
    private UserStatisticsService $userStatisticsService;

    public function __construct(UserStatisticsService  $userStatisticsService){
        $this->userStatisticsService = $userStatisticsService;
    }

    public function userStatisticsOverview(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userStatisticsService->userStatisticsOverview();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}