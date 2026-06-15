<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Http\Requests\Plan\CreateStudyPlanRequest;
use App\Services\StudyPlanService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class PlanController extends Controller
{
    private StudyPlanService $studyPlanService;

    public function __construct(StudyPlanService $studyPlanService){
        $this->studyPlanService = $studyPlanService;
    }



public function createStudyPlan(
    CreateStudyPlanRequest $request
): JsonResponse {

    $data = [];

    try {

        $data = $this->studyPlanService
            ->createStudyPlan($request->validated());

        return Response::Success(
            $data['plan'],
            $data['message']
        );

    } catch (\Throwable $th) {

        $message = $th->getMessage();
        $errors[] = $message;

        return Response::Error(
            $data,
            $message,
            $errors
        );
    }
}
}
