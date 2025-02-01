<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Acl\PermissionController;
use App\Http\Controllers\Api\V1\Acl\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')
    ->prefix('acl')
    ->group(static function () {

        Route::prefix('roles')->group(static function () {
            Route::get('/', [RoleController::class, 'items']);
            Route::post('/', [RoleController::class, 'create']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::put('/{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'delete']);
        });

        Route::prefix('permissions')->group(static function () {
            Route::get('/', [PermissionController::class, 'items']);
        });
    });
