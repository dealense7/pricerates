<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\BindingServiceProvider;
use App\Providers\PassportServiceProvider;

return [
    AppServiceProvider::class,
    BindingServiceProvider::class,
    PassportServiceProvider::class,
];
