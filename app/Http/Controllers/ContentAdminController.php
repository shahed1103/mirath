<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;

use App\Services\ContentAdminService;
use App\Http\Requests\Admin\AddNewClassificationRequest;
use App\Http\Requests\Admin\AddNewBookRequest;
use App\Http\Requests\Admin\AddNewChapterRequest;
use App\Http\Requests\Admin\EditQuestionRequest;
use App\Http\Requests\Admin\EditOpenQuestionRequest;
use App\Http\Requests\Admin\AddQuestionRequest;
use App\Http\Requests\Admin\AddOpenQuestionToChapterRequest;
use App\Http\Requests\Admin\EditClassificationRequest;
use App\Http\Requests\Admin\EditBookRequest;
use App\Http\Requests\Admin\EditChapterRequest;
use App\Http\Requests\Admin\EditChapterContentRequest;



use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class ContentAdminController extends Controller
{
    private ContentAdminService $contentAdminService;

    public function __construct(ContentAdminService  $contentAdminService){
        $this->contentAdminService = $contentAdminService;
    }

    public function getBookDetailsAdmin($bookId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->contentAdminService->getBookDetailsAdmin($bookId);
            return Response::Success($data['chapters'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getChapterDetailsAdmin($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->contentAdminService->getChapterDetailsAdmin($chapterId);
            return Response::Success($data['contents'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function addNewClassification(AddNewClassificationRequest $request): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->addNewClassification($request);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            report($th);
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function addNewBook(AddNewBookRequest $request, $classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->addNewBook($request, $classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function addNewChapter(AddNewChapterRequest $request, $bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->addNewChapter($request, $bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function editClassification(EditClassificationRequest $request, $classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->editClassification($request, $classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function editBook(EditBookRequest $request, $bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->editBook($request, $bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function editChapter(EditChapterRequest $request, $chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->editChapter($request, $chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function editChapterContent(EditChapterContentRequest $request, $contentId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->editChapterContent($request, $contentId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function deleteClassification($classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->deleteClassification($classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            report($th);
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function deleteBook($bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->deleteBook($bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            report($th);
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function deleteChapter($chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->contentAdminService->deleteChapter($chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            report($th);
            return Response::Error($data , $message , $errors , $code);
        }
    }
}
