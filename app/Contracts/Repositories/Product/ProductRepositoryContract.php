<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Product;

use App\Support\Collection;

interface ProductRepositoryContract
{
    public function getMostPopularItems(): Collection;

    public function getRandomCategoryItems(): Collection;
}
