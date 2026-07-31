<?php

namespace App\Services\ITAdmin;

use App\Models\ApiRequestLog;

class ApiMonitoringService
{
public function getApiMonitoring(int $days = 30): array
{
    return [
        'total_requests' => $this->getTotalRequests($days),
        'failed_requests' => $this->getFailedRequests($days),
        'success_rate' => $this->getSuccessRate($days),
        'average_response_time' => $this->getAverageResponseTime($days),
        'last_request' => $this->getLastRequest($days),
        'slowest_endpoints' => $this->getSlowestEndpoints($days),
        'most_requested_endpoints' => $this->getMostRequestedEndpoints($days),
        'status_codes' => $this->getStatusCodes($days),
    ];
}

    private function getTotalRequests(int $days): int
{
    return ApiRequestLog::where(
        'created_at',
        '>=',
        now()->subDays($days)
    )->count();
}



private function getFailedRequests(int $days): array
{
    $query = ApiRequestLog::where(
        'created_at',
        '>=',
        now()->subDays($days)
    );

    return [
        'total' => (clone $query)
            ->where('status_code', '>=', 400)
            ->count(),

        'client_errors' => (clone $query)
            ->whereBetween('status_code', [400, 499])
            ->count(),

        'server_errors' => (clone $query)
            ->where('status_code', '>=', 500)
            ->count(),
    ];
}

private function getSuccessRate(int $days): string
{
    $total = $this->getTotalRequests($days);

    if ($total === 0) {
        return '0%';
    }

    $success = ApiRequestLog::where(
        'created_at',
        '>=',
        now()->subDays($days)
    )
    ->whereBetween('status_code', [200, 299])
    ->count();

    return round(($success / $total) * 100, 2) . '%';
}


private function getAverageResponseTime(int $days): string
{
    $average = ApiRequestLog::where(
        'created_at',
        '>=',
        now()->subDays($days)
    )->avg('response_time');

    return round($average ?? 0) . ' ms';
}



private function getMostRequestedEndpoints(int $days): array
{
    return ApiRequestLog::selectRaw('endpoint, COUNT(*) as requests')
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('endpoint')
        ->orderByDesc('requests')
        ->limit(5)
        ->get()
        ->map(function ($endpoint) {
            return [
                'endpoint' => $endpoint->endpoint,
                'requests' => $endpoint->requests,
            ];
        })
        ->toArray();
}


private function getSlowestEndpoints(int $days): array
{
    return ApiRequestLog::selectRaw('endpoint, AVG(response_time) as average_time')
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('endpoint')
        ->orderByDesc('average_time')
        ->limit(5)
        ->get()
        ->map(function ($endpoint) {
            return [
                'endpoint' => $endpoint->endpoint,
                'average_response_time' => round($endpoint->average_time) . ' ms',
            ];
        })
        ->toArray();
}

private function getStatusCodes(int $days): array
{
    return ApiRequestLog::selectRaw('status_code, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('status_code')
        ->orderBy('status_code')
        ->get()
        ->toArray();
}

private function getLastRequest(int $days): array
{
    $request = ApiRequestLog::where(
        'created_at',
        '>=',
        now()->subDays($days)
    )
    ->latest()
    ->first();

    if (!$request) {
        return [];
    }

    return [
        'endpoint' => $request->endpoint,
        'method' => $request->method,
        'status_code' => $request->status_code,
        'response_time' => $request->response_time . ' ms',
        'requested_at' => $request->created_at,
    ];
}
}
