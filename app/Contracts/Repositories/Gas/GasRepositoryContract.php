<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Gas;

use App\Support\Collection;

interface GasRepositoryContract
{
    public function getItems(): Collection;
}
