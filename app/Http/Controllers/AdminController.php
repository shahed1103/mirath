<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\AdminService;
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

    public function addNewClassification(AddNewClassificationRequest $request): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->addNewClassification($request);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function addNewBook(AddNewBookRequest $request, $classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->addNewBook($request, $classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }
    
    public function addNewChapter(AddNewChapterRequest $request, $bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->addNewChapter($request, $bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editClassification(EditClassificationRequest $request, $classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editClassification($request, $classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editBook(EditBookRequest $request, $bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editBook($request, $bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editChapter(EditChapterRequest $request, $chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editChapter($request, $chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editChapterContent(EditChapterContentRequest $request, $contentId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editChapterContent($request, $contentId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function deleteClassification($classificationId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteClassification($classificationId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }    
    }

    public function deleteBook($bookId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteBook($bookId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }    
    }

    public function deleteChapter($chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteChapter($chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }    
    }

    public function allChapterQuestionsWithAnswers($chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->allChapterQuestionsWithAnswers($chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function allChapterOpenQuestionsWithAnswers($chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->allChapterOpenQuestionsWithAnswers($chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function addQuestionToChapter(AddQuestionRequest $request, $chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->addQuestionToChapter($request, $chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function addOpenQuestionToChapter(AddOpenQuestionToChapterRequest $request, $chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->addOpenQuestionToChapter($request, $chapterId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editQuestion(EditQuestionRequest $request, $questionId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editQuestion($request, $questionId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function editChoice(Request $request, $choiceId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editChoice($request, $choiceId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }    
    }

    public function editOpenQuestion(EditOpenQuestionRequest $request, $openQuestionId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->editOpenQuestion($request, $openQuestionId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function deleteQuestion($questionId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteQuestion($questionId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }

    public function deleteChoice($choiceId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteChoice($choiceId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }    
    }

    public function deleteOpenQuestion($openQuestionId): JsonResponse {
        $data = [];
        try{
            $data = $this->adminService->deleteOpenQuestion($openQuestionId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }
}