<?php

declare(strict_types=1);

namespace App\Services\V1\Gas;

use App\Contracts\Repositories\Currency\CurrencyRepositoryContract;
use App\Contracts\Repositories\Gas\GasRepositoryContract;
use App\Contracts\Services\Currency\CurrencyServiceContract;
use App\Contracts\Services\Gas\GasServiceContract;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Support\Facades\Vite;

class GasService extends Service implements GasServiceContract
{
    public function __construct(
        protected GasRepositoryContract $repository,
    )
    {
        //
    }

    public function getItems(): Collection
    {
        return $this->repository->getItems()->transform(function ($item) {
            return [
                'id'           => $item->getId(),
                'name'         => $item->getName(),
                'tag'          => $item->getTag(),
                'price'        => number_format($item->getPrice() / 100, 2),
                'providerName' => $item->provider->getName(),
                'providerLogo' => Vite::asset('resources/imgs/' . $item->provider->getLogoUrl()),
            ];
        });
    }
}
