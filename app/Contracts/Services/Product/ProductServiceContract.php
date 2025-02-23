<?php

declare(strict_types=1);

namespace App\Contracts\Services\Product;

use App\Support\Collection;

interface ProductServiceContract
{
    public function getMostPopularItems(): Collection;
}
