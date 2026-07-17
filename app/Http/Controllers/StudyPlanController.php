<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Http\Requests\Plan\CalculateStudyPlanRequest;
use App\Services\PlanCalculatorService;


class StudyPlanController extends Controller
{
private PlanCalculatorService $planCalculatorService;

public function __construct(PlanCalculatorService $planCalculatorService)
{
    $this->planCalculatorService = $planCalculatorService;
}

public function calculate(
    CalculateStudyPlanRequest $request
): JsonResponse
{
    try {

        $result = $this->planCalculatorService->calculate(
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
