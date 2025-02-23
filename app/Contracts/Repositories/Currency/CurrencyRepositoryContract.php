<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Currency;

use App\Support\Collection;

interface CurrencyRepositoryContract
{
    public function getItems(): Collection;
}
