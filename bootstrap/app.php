<?php

declare(strict_types=1);

use App\Http\Middleware\Auth\OtpVerifiedMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health : '/up',
        then : static function () {
            $files = glob(base_path('routes/api/*.php'));

            $router = app()->make('router');
            foreach ($files as $file) {
                $router->middleware('api')
                    ->prefix('api')
                    ->group($file);
            }
        },
    )
   ->withMiddleware(static function (Middleware $middleware) {
       $middleware->api(append:[
           OtpVerifiedMiddleware::class,
       ]);
   })
   ->withExceptions(static function (Exceptions $exceptions) {
       //
   })->create();
