<?php

declare(strict_types=1);

namespace App\Services\V1\Currency;

use App\Contracts\Repositories\Currency\CurrencyRepositoryContract;
use App\Contracts\Services\Currency\CurrencyServiceContract;
use App\Services\Service;
use App\Support\Collection;

class CurrencyService extends Service implements CurrencyServiceContract
{
    public function __construct(
        protected CurrencyRepositoryContract $repository,
    ) {
        //
    }

    public function getItems(): Collection
    {
        return $this->repository->getItems();
    }
}
