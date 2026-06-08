<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class ProfileController extends Controller
{
    private ProfileService $profileService;

    public function __construct(ProfileService $profileService){
        $this->profileService = $profileService;
    }


public function getStudentStatistics(): JsonResponse
{
    $data = [];

    try {
        $data = $this->examService->getStudentStatistics();

        return Response::Success(
            $data['statistics'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}


public function getAllLibraryBooks(): JsonResponse
{
    $data = [];

    try {
        $data = $this->libraryBookService->getAllLibraryBooks();

        return Response::Success(
            $data['books'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}
}
