<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\QuestionAdminService;
use App\Http\Requests\Admin\EditQuestionRequest;
use App\Http\Requests\Admin\EditOpenQuestionRequest;
use App\Http\Requests\Admin\AddQuestionRequest;
use App\Http\Requests\Admin\AddOpenQuestionToChapterRequest;

use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class QuestionAdminController extends Controller
{
    private QuestionAdminService $questionAdminService;

    public function __construct(QuestionAdminService  $questionAdminService){
        $this->questionAdminService = $questionAdminService;
    }

    public function allChapterQuestionsWithAnswers($chapterId): JsonResponse {
        $data = [];
        try{
            $data = $this->questionAdminService->allChapterQuestionsWithAnswers($chapterId);
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
            $data = $this->questionAdminService->allChapterOpenQuestionsWithAnswers($chapterId);
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
            $data = $this->questionAdminService->addQuestionToChapter($request, $chapterId);
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
            $data = $this->questionAdminService->addOpenQuestionToChapter($request, $chapterId);
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
            $data = $this->questionAdminService->editQuestion($request, $questionId);
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
            $data = $this->questionAdminService->editChoice($request, $choiceId);
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
            $data = $this->questionAdminService->editOpenQuestion($request, $openQuestionId);
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
            $data = $this->questionAdminService->deleteQuestion($questionId);
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
            $data = $this->questionAdminService->deleteChoice($choiceId);
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
            $data = $this->questionAdminService->deleteOpenQuestion($openQuestionId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }    
    }
}