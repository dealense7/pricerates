<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->middleware('auth:api')
    ->group(static function () {
        Route::get('/', [UserController::class, 'items']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::post('/deactivate/{id}', [UserController::class, 'deactivate']);
        Route::post('/activate/{id}', [UserController::class, 'activate']);
        Route::delete('{id}', [UserController::class, 'delete']);
        Route::post('restore/{id}', [UserController::class, 'restore']);

        Route::get('{id}/acl', [UserController::class, 'getAcl']);
        Route::put('{id}/permissions', [UserController::class, 'attachPermissions']);
        Route::put('{id}/roles', [UserController::class, 'attachRoles']);
    });
