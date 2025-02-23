<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-gas-data')->everyFourHours();
Schedule::command('app:fetch-currency-data')->everyFourHours();
