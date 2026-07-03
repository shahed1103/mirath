<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class AdminController extends Controller
{
    private AdminService $adminService;

    public function __construct(AdminService  $adminService){
        $this->adminService = $adminService;
    }

    public function getAllUsers(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->adminService->getAllUsers();
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
            $data = $this->adminService->getAllFeedbacks();
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getBookDetailsAdmin($bookId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->adminService->getBookDetailsAdmin($bookId);
            return Response::Success($data['chapters'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getChapterDetailsAdmin($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->adminService->getChapterDetailsAdmin($chapterId);
            return Response::Success($data['contents'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}