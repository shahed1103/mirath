<?php

namespace App\Http\Controllers;

use App\Http\Requests\Plan\CalculateStudyPlanRequest;
use App\Http\Requests\Plan\StoreStudyPlanRequest;
use App\Services\StudyPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Responses\response;

class StudyPlanController extends Controller
{
    protected StudyPlanService $studyPlanService;

    public function __construct(StudyPlanService $studyPlanService)
    {
        $this->studyPlanService = $studyPlanService;
    }


   public function calculatePlan(CalculateStudyPlanRequest $request): JsonResponse
{
    $data = [];
    try {
        $data = $this->studyPlanService->calculatePlan(
            $request->validated()
        );

        return Response::Success(
            $data['plan'],
            $data['message']
        );

    } catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error(   $data, $message,   $errors );
    }
}



public function createPlan(StoreStudyPlanRequest $request): JsonResponse
{
    $data = [];
    try {
        $data = $this->studyPlanService->createPlan(
            $request->validated()
        );
        return Response::Success(
            $data['plan'],
            $data['message']
        );
    } catch (Throwable $th) {
        $message = $th->getMessage();
        $errors[] = $message;
        return Response::Error( $data, $message, $errors );
    }
}
}




