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
            'average_response_time' => $this->getAverageResponseTime($days),
            'slowest_endpoint' => $this->getSlowestEndpoint($days),
            'most_requested_endpoint' => $this->getMostRequestedEndpoint($days),
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



private function getFailedRequests(int $days): int
{
    return ApiRequestLog::where('created_at', '>=', now()->subDays($days))
        ->where('status_code', '>=', 400)
        ->count();
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



private function getMostRequestedEndpoint(int $days): array
{
    $endpoint = ApiRequestLog::selectRaw('endpoint, COUNT(*) as requests')
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('endpoint')
        ->orderByDesc('requests')
        ->first();

    if (!$endpoint) {
        return [];
    }

    return [
        'endpoint' => $endpoint->endpoint,
        'requests' => $endpoint->requests,
    ];
}



private function getSlowestEndpoint(int $days): array
{
    $endpoint = ApiRequestLog::selectRaw('endpoint, AVG(response_time) as average_time')
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('endpoint')
        ->orderByDesc('average_time')
        ->first();

    if (!$endpoint) {
        return [];
    }

    return [
        'endpoint' => $endpoint->endpoint,
        'average_response_time' => round($endpoint->average_time) . ' ms',
    ];
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
}
