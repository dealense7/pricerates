<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Currency;

use App\Models\User\User;
use App\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CurrencyRepositoryContract
{
    public function getItems(): Collection;
}
