<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function getRequest(): Request
    {
        return app(Request::class);
    }
}
