<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;
use App\Services\ProfileService;
use App\Http\Requests\ConfirmBookRedemptionRequest;
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
        $data = $this->profileService->getStudentStatistics();

        return Response::Success(
            $data['statistics'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;
        return Response::Error($data, $message, $errors);
    }
}


public function getMyPoints(): JsonResponse
{
    $data = [];

    try {
        $data = $this->profileService->getMyPoints();

        return Response::Success(
            $data['points'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}



public function getAllLibraryBooks(): JsonResponse
{
    $data = [];

    try {
        $data = $this->profileService->getAllLibraryBooks();

        return Response::Success(
            $data['books'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}


public function addBookToCart($bookId): JsonResponse
{
    $data = [];

    try {
        $data = $this->profileService->addBookToCart($bookId);

        return Response::Success([], $data['message']);
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}


public function getCartItems(): JsonResponse
{
    $data = [];
    try {
        $data = $this->profileService->getCartItems();
        return Response::Success(
            [
                'items' => $data['cart_items'],
                'total_points' => $data['total_points']
            ],
            $data['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}


public function removeBookFromCart($bookId): JsonResponse
{
    $data = [];

    try {
        $data = $this->profileService->removeBookFromCart($bookId);

        return Response::Success([], $data['message']);
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error($data, $message, $errors);
    }
}


public function requestBookRedemption(
    ConfirmBookRedemptionRequest $request
): JsonResponse
{
    $data = [];
    try {
        $data = $this->profileService
            ->requestBookRedemption($request->validated()['book_ids']);
return Response::Success(
    [
        'library_location' => $data['library_location'],
        'working_hours' => $data['working_hours'],
    ],
    $data['message']
);

    } catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        $errors[] = $message;
        return Response::Error($data, $message, $errors);
    }
}

public function getLastUserExams(): JsonResponse
{
    try {
        $result = $this->profileService->getLastUserExams(3);

        return Response::Success(
            $result['data'],
            $result['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        return Response::Error([], $message, [$message]);
    }
}


public function getAllUserExams(): JsonResponse
{
    try {
        $result = $this->profileService->getAllUserExams();
        return Response::Success(
            $result['data'],
            $result['message']
        );
    }
    catch (Throwable $th) {
        report($th);
        $message = $th->getMessage();
        return Response::Error([], $message, [$message]);
    }
}
}

