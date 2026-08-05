<?php

namespace App\Services\ITAdmin;

use App\Models\ErrorLog;

class ErrorMonitoringService
{
    public function getErrorMonitoring(int $days = 30): array
    {
        return [

            'total_errors' => $this->getTotalErrors($days),

            'critical_errors' => $this->getCriticalErrors($days),

            'today_errors' => $this->getTodayErrors(),

            'top_exceptions' => $this->getTopExceptions($days),

            'latest_errors' => $this->getLatestErrors(),

            'errors_by_day' => $this->getErrorsByDay($days),

        ];
    }

    private function getTotalErrors(int $days): int
    {
        return ErrorLog::where(
            'created_at',
            '>=',
            now()->subDays($days)
        )->count();
    }

    private function getCriticalErrors(int $days): int
    {
        return ErrorLog::where(
            'created_at',
            '>=',
            now()->subDays($days)
        )
        ->where('status_code', '>=', 500)
        ->count();
    }

    private function getTodayErrors(): int
    {
        return ErrorLog::whereDate(
            'created_at',
            today()
        )->count();
    }

    private function getTopExceptions(): array
    {
        return ErrorLog::selectRaw('exception, COUNT(*) as total')
            ->groupBy('exception')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {

                return [

                    'exception' => $item->exception,

                    'count' => $item->total,

                ];

            })
            ->toArray();
    }

    private function getLatestErrors(): array
    {
        return ErrorLog::latest()
            ->limit(10)
            ->get()
            ->map(function ($error) {

                return [

                    'exception' => $error->exception,

                    'message' => $error->message,

                    'status_code' => $error->status_code,

                    'endpoint' => $error->endpoint,

                    'method' => $error->method,

                    'file' => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $error->file),

                    'line' => $error->line,

                    'created_at' => $error->created_at,

                ];

            })
            ->toArray();
    }

    private function getErrorsByDay(int $days): array
    {
        return ErrorLog::selectRaw("
                DATE(created_at) as date,
                COUNT(*) as total
            ")
            ->where(
                'created_at',
                '>=',
                now()->subDays($days)
            )
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
