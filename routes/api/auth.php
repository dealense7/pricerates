<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Middleware\Auth\OtpVerifiedMiddleware;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')
    ->group(static function () {
        Route::post('token', [AuthController::class, 'token'])->withoutMiddleware([OtpVerifiedMiddleware::class]);

        Route::middleware(['auth:api'])->group(static function () {
            Route::get('me', [AuthController::class, 'currentUser']);
            Route::get('acl', [AuthController::class, 'permissions']);
            Route::delete('token', [AuthController::class, 'revokeToken']);
            Route::put('password/update', [UserController::class, 'passwordUpdate']);
        });
    });
