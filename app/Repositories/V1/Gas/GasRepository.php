<?php

declare(strict_types=1);

namespace App\Repositories\V1\Gas;

use App\Contracts\Repositories\Currency\CurrencyRepositoryContract;
use App\Contracts\Repositories\Gas\GasRepositoryContract;
use App\Models\Currency\CurrencyRate;
use App\Models\Gas\GasRate;
use App\Repositories\Repository;
use App\Support\Collection;

class GasRepository extends Repository implements GasRepositoryContract
{
    public function getItems(): Collection
    {
        $model = $this->getModel();

        $query = $model->newQuery()
            ->with(['provider'])
            ->where('status', true);

        /** @var \App\Support\Collection $items */
        $items = $query->get();

        return $items;
    }

    public function getModel(): GasRate
    {
        return new GasRate();
    }
}
