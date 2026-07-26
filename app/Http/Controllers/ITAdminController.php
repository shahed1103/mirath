<?php

namespace App\Http\Controllers;

use Throwable;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use Illuminate\Http\JsonResponse;
use App\Services\ITAdmin\SystemHealthService;

class ITAdminController extends Controller
{
    public function __construct(
        private readonly SystemHealthService $systemHealthService
    ) {
    }

    public function getSystemHealth(): JsonResponse
    {
        try {

            $result = $this->systemHealthService->getSystemHealth();

            return Response::Success(
                $result,
                'System health retrieved successfully.'
            );

        } catch (Throwable $th) {

            return Response::Error(
                [],
                $th->getMessage(),
                [$th->getMessage()]
            );
        }
    }
}
