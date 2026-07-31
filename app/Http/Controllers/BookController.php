<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class BookController extends Controller
{
    private BookService $bookService;

    public function __construct(BookService  $bookService){
        $this->bookService = $bookService;
    }

    public function getClassificationDetails($classificationId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->bookService->getClassificationDetails($classificationId);
            return Response::Success($data['books'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getBookDetails($bookId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->bookService->getBookDetails($bookId);
            return Response::Success($data['chapters'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getChapterDetails($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->bookService->getChapterDetails($chapterId);
            return Response::Success($data['contents'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::ErrorX($data , $message , $errors , $code);
        }
    }

}
