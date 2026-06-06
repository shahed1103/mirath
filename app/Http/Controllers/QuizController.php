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

    public function __construct(QuizService  $quizService){
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
            return Response::Error($data , $message , $errors);
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
            return Response::Error($data , $message , $errors);
        }
    }}
