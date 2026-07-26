<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class TrackApplicationStart
{
    public function handle($request, Closure $next)
    {
        if (! Cache::has('application_started_at')) {
            Cache::forever('application_started_at', now());
        }

        return $next($request);
    }
}

