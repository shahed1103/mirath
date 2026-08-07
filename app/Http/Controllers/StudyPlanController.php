<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\response;
use App\Services\StudyPlanService;
use App\Http\Requests\StudyPlan\CalculateStudyPlanRequest;
use App\Http\Requests\StudyPlan\StudyPlanRequest;

class StudyPlanController extends Controller
{
    public function __construct(
        private readonly StudyPlanService $studyPlanService
    ) {
    }

    public function calculatePlan(
        CalculateStudyPlanRequest $request
    ): JsonResponse {

        try {

            $result = $this->studyPlanService
                ->calculate($request->validated());

            return Response::Success(
                $result['data'],
                $result['message']
            );

        } catch (Throwable $th) {

            $message = $th->getMessage();

            return Response::Error(
                [],
                $message,
                [$message]
            );

        }

    }

    public function createPlan(
        StudyPlanRequest $request
    ): JsonResponse {

        try {

            $result = $this->studyPlanService->create(
                auth()->id(),
                $request->validated()
            );

            return Response::Success(
                $result['data'],
                $result['message']
            );

        } catch (Throwable $th) {

            $message = $th->getMessage();

            return Response::Error(
                [],
                $message,
                [$message]
            );

        }

    }

}
