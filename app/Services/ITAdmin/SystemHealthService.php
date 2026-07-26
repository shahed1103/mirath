<?php

namespace App\Services\ITAdmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthService
{


public function getSystemHealth(): array
{
    return [
        'server_status' => $this->getServerStatus(),
        'uptime' => $this->getUptime(),
        'database' => ['size' => $this->getDatabaseSize(),],
        'memory_usage' => $this->getMemoryUsage(),
        'application' => $this->getApplicationInfo(),

    ];
}

private function getServerStatus(): array
{
    return [
        'is_online' => true,
        'status' => 'online',
        'checked_at' => now(),
    ];
}

private function getUptime(): string
{
    $started = Cache::get('application_started_at');

    if (!$started) {
        return 'Unknown';
    }

    return $started->diffForHumans(now(), [
        'parts' => 3,
        'short' => false,
    ]);
}

private function getDatabaseSize(): string
{

try {
        $result = DB::select("
        SELECT
        ROUND(SUM(data_length + index_length)/1024/1024,2) AS size
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
    ");
} catch (\Throwable $e) {
    return 'Unknown';
}


    return ($result[0]->size ?? 0).' MB';
}

private function getMemoryUsage(): array
{


return [

    'current' => $this->formatBytes(memory_get_usage(true)),

    'peak' => $this->formatBytes(memory_get_peak_usage(true)),

    'limit' => ini_get('memory_limit'),

];

}

private function formatBytes(int|float $bytes): string
{
    $units = ['B','KB','MB','GB','TB'];

    $bytes = max($bytes, 0);

    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));

    $pow = min($pow, count($units)-1);

    $bytes /= pow(1024, $pow);

    return round($bytes,2).' '.$units[$pow];
}

private function getApplicationInfo(): array
{
    return [
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
        'debug' => config('app.debug'),
        'server_time' => now(),
        'timezone' => config('app.timezone'),
    ];
}
}
