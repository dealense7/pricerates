<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Core\CountryController;
use Illuminate\Support\Facades\Route;

Route::prefix('config')->group(static function () {
    Route::get('countries/', [CountryController::class, 'items']);
});
