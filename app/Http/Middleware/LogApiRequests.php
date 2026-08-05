<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiRequestLog;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        // تجاهل لوحة الـ IT و Telescope
        if (
            $request->is('api/it-admin/*') ||
            $request->is('telescope/*')
        ) {
            return $next($request);
        }

        $start = microtime(true);

        $response = $next($request);

        $responseTime = (int) round((microtime(true) - $start) * 1000);

        ApiRequestLog::create([
            'user_id'       => auth()->id(),
            'method'        => $request->method(),
            'endpoint'      => '/' . ltrim($request->path(), '/'),
            'status_code'   => $response->getStatusCode(),
            'response_time' => $responseTime,
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return $response;
    }
}
