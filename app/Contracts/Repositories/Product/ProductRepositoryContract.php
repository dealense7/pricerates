<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Product;

use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryContract
{
    public function getItems(array $filters = []): LengthAwarePaginator;

    public function getMostPopularItems(): Collection;

    public function getRandomCategoryItems(): Collection;
}
