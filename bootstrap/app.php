<?php

use App\Exceptions\InvalidRequestException;
use App\Http\Middleware\AcceptHeader;
use App\Http\Middleware\SetWithoutWrapping;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
//            Route::middleware('api')
//                ->prefix('common')
//                ->name('common.')
//                ->group(base_path('routes/common.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 设置中间件
        $middleware->api(prepend: [
            // 设置默认请求头
            AcceptHeader::class,
            SetWithoutWrapping::class,
        ]);

    })
    ->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) {
//        Schedule::command('telescope:prune')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 不报告的异常
        $exceptions->dontReport([
            InvalidRequestException::class
        ]);
        // 捕获异常
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                return response()->json([
                    'message' => '令牌无效或过期!',
                ])->setStatusCode(401);
            }
            return $response;
        });
    })->create();
