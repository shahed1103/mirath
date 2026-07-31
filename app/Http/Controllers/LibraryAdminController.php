<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\LibraryAdminService;
use App\Http\Requests\Admin\StoreLibraryBookRequest;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;





class LibraryAdminController extends Controller{

    private LibraryAdminService $libraryService;

    public function __construct(LibraryAdminService $libraryService){
        $this->libraryService = $libraryService;
    }

public function storeLibraryBook(StoreLibraryBookRequest $request): JsonResponse
{
    $data = [];

    try {

        $data = $this->libraryService->storeLibraryBook($request);

        return Response::Success(
            $data['book'],
            $data['message']
        );

    } catch (Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}

public function getAllLibraryBooks(): JsonResponse
{
    $data = [];

    try {
        $data = $this->libraryService->getAllLibraryBooks();

        return Response::Success(
            $data['books'],
            $data['message']
        );
    }
    catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}


public function getAllBookRedemptions(): JsonResponse
{
    $data = [];

    try {

        $data = $this->libraryService->getAllBookRedemptions();

        return Response::Success(
            $data['redemptions'],
            $data['message']
        );

    } catch (Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}


public function getMostRedeemedBooks(): JsonResponse
{
    $data = [];

    try {

        $data = $this->libraryService->getMostRedeemedBooks();

        return Response::Success(
            $data['books'],
            $data['message']
        );

    } catch (Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}


public function getMonthlyRedeemedPoints(): JsonResponse
{
    $data = [];
    try {
        $data = $this->libraryService->getMonthlyRedeemedPoints();
        return Response::Success(
            $data['points'],
            $data['message']
        );

    } catch (Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}

public function getBookRedemptionStatistics(): JsonResponse
{
    $data = [];

    try {

        $data = $this->libraryService->getBookRedemptionStatistics();

        return Response::Success(
            $data,
            $data['message']
        );

    } catch (Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;
        report($th);

        return Response::Error($data, $message, $errors);
    }
}

public function confirmBookRedemption(int $redemptionId): JsonResponse
{
    $data = [];

    try {
        $data = $this->libraryService->confirmBookRedemption($redemptionId);
        return Response::Success(
            $data,
            $data['message']
        );
    } catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;
        report($th);
        return Response::Error($data, $message, $errors);
    }
}

public function getCompletedBookRedemptions(): JsonResponse
{
    try {

        $result = $this->libraryService->getCompletedBookRedemptions();

        return Response::Success(
            $result,
            'Completed book redemptions retrieved successfully.'
        );

    } catch (Throwable $th) {
         report($th);

        return Response::Error(
            [],
            $th->getMessage(),
            [$th->getMessage()]
        );
    }
}
}
