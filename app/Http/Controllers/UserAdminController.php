<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\UserAdminService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Http\Controllers\Controller;

class UserAdminController extends Controller
{
    private UserAdminService $userAdminService;

    public function __construct(UserAdminService  $userAdminService){
        $this->userAdminService = $userAdminService;
    }

    public function getAllUsers(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userAdminService->getAllUsers();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getAllFeedbacks(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userAdminService->getAllFeedbacks();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function userStatisticsOverview(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->userAdminService->userStatisticsOverview();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}