<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class QuizController extends Controller
{
    private QuizService $quizService;

    public function __construct(QuizService $quizService){
        $this->quizService = $quizService;
    }
    
    public function startQuiz($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->quizService->startQuiz($chapterId);
            return Response::Success($data['quiz'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::ErrorX($data , $message , $errors , $code);
        }
    }

    public function submitAnswer($sessionId , $questionId , $choiceId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->quizService->submitAnswer($sessionId , $questionId , $choiceId);
            return Response::Success($data['quiz'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }

    public function endQuiz($sessionId): JsonResponse {
        $data = [] ;
        // try{
            $data = $this->quizService->endQuiz($sessionId);
            return Response::Success($data['quiz'], $data['message']);
        // }
        // catch(Throwable $th){
        //     $message = $th->getMessage();
        //     $errors [] = $message;
        //     $code = $th->getCode();
        //     return Response::Error($data , $message , $errors , $code);
        // }
    }

    public function quizResult($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->quizService->quizResult($chapterId);
            return Response::Success($data['quiz'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }

    public function getOpenQuestion($chapterId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->quizService->getOpenQuestion($chapterId);
            return Response::Success($data['questions'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            $code = $th->getCode();
            return Response::Error($data , $message , $errors , $code);
        }
    }

    // public function getOpenQuestion(Request $request , $chapterId): JsonResponse {
    //     $data = [] ;
    //     try{
    //         $index = $request->integer('index', 0);
    //         $data = $this->quizService->getOpenQuestion($chapterId , $index);
    //         return Response::Success($data['question'], $data['message']);
    //     }
    //     catch(Throwable $th){
    //         $message = $th->getMessage();
    //         $errors [] = $message;
    //         $code = $th->getCode();
    //         return Response::Error($data , $message , $errors , $code);
    //     }
    // }

    // public function getAnswer($questionId): JsonResponse {
    //     $data = [] ;
    //     try{
    //         $data = $this->quizService->getAnswer($questionId);
    //         return Response::Success($data['answer'], $data['message']);
    //     }
    //     catch(Throwable $th){
    //         $message = $th->getMessage();
    //         $errors [] = $message;
    //         return Response::Error($data , $message , $errors);
    //     }
    // }
}
