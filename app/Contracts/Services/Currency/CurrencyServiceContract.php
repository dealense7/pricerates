<?php

declare(strict_types=1);

namespace App\Contracts\Services\Currency;

use App\Support\Collection;

interface CurrencyServiceContract
{
    public function getItems(): Collection;
}
