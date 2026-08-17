<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Response;
use App\Services\HomeService;
use App\Services\StudyPlanService;
use App\Http\Requests\Home\ProgressRequest;
use App\Http\Requests\Home\FeedbackRequest;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class HomeController extends Controller
{

    public function __construct(
        private HomeService $homeService,
        private StudyPlanService $studyPlanService
    ){}

    public function getClassifications(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->homeService->getClassifications();
            return Response::Success($data, 'all classifications are retrieved successfully');
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function getFeatures(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->homeService->getFeatures();
            return Response::Success($data, 'all features are retrieved successfully');
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function getContinueReading(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->homeService->getContinueReading();
            return Response::Success($data, 'book to continue reading is retrived successfully');
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function getHome(): JsonResponse {
        $data = [] ;
        try{
        $data = [
            'continue_reading' => $this->homeService->getContinueReading(),
            'classifications' => $this->homeService->getClassifications(),
            'features' => $this->homeService->getFeatures(),
            'plan' => $this->studyPlanService->getPlanProgress(),
        ];
        return Response::Success($data,  'Home data retrieved successfully');
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function updateProgress(ProgressRequest $request , $contentId): JsonResponse {
        $data = [] ;
        try{
            $data = $this->homeService->updateProgress($request, $contentId);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }

    public function addFeedback(FeedbackRequest $request): JsonResponse {
        $data = [] ;
        try{
            $data = $this->homeService->addFeedback($request);
            return Response::Success($data['data'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            report($th);
            return Response::Error($data , $message , $errors);
        }
    }
}
