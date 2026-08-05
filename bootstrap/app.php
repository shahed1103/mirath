<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
       $middleware->api(append: [
    \App\Http\Middleware\LogApiRequests::class,
]);
    })

->withExceptions(function (Exceptions $exceptions) {

    $exceptions->report(function (Throwable $e) {

        // لا نسجل أخطاء التحقق من المدخلات أو 404
        if (
            $e instanceof ValidationException ||
            $e instanceof NotFoundHttpException
        ) {
            return;
        }

        try {

            $request = request();

            ErrorLog::create([

                'user_id' => auth()->check() ? auth()->id() : null,
                  'file' => $e->getFile(),

                  'line' => $e->getLine(),

                'exception' => class_basename($e),

                'message' => $e->getMessage(),

                'status_code' => method_exists($e, 'getStatusCode')
                    ? $e->getStatusCode()
                    : 500,

                'endpoint' => $request instanceof Request
                    ? '/' . ltrim($request->path(), '/')
                    : null,

                'method' => $request instanceof Request
                    ? $request->method()
                    : null,

                'ip' => $request instanceof Request
                    ? $request->ip()
                    : null,

            ]);

        } catch (Throwable $ignored) {
            // نتجاهل أي خطأ أثناء تسجيل الخطأ نفسه
        }

    });


    })->create();
