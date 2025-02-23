<?php

declare(strict_types=1);

namespace App\Contracts\Services\Gas;

use App\Support\Collection;

interface GasServiceContract
{
    public function getItems(): Collection;
}
