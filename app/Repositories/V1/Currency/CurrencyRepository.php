<?php

declare(strict_types=1);

namespace App\Repositories\V1\Currency;

use App\Contracts\Repositories\Currency\CurrencyRepositoryContract;
use App\Models\Currency\CurrencyRate;
use App\Repositories\Repository;
use App\Support\Collection;

class CurrencyRepository extends Repository implements CurrencyRepositoryContract
{
    public function getItems(): Collection
    {
        $model = $this->getModel();

        $query = $model->newQuery()
            ->with(['provider', 'currency'])
            ->whereDate('date', today());

        /** @var Collection $items */
        $items = $query->get();

        return $items;
    }

    public function getModel(): CurrencyRate
    {
        return new CurrencyRate();
    }
}
