<?php

declare(strict_types=1);

namespace App\Services\V1\Products;

use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Contracts\Services\Product\ProductServiceContract;
use App\Services\Service;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends Service implements ProductServiceContract
{
    public function __construct(
        protected ProductRepositoryContract $repository,
    )
    {
        //
    }

    public function getItems(array $filters = []): LengthAwarePaginator
    {
        $result = $this->repository->getItems($filters);

        $result->setCollection($this->mapItems(Collection::make($result->items())));
        return $result;
    }

    public function getRandomCategoryItems(): Collection
    {
        return $this->repository->getRandomCategoryItems()->transform(function ($item) {
            $item->setRelation('products', $this->mapItems($item->products->random(7)));
            return $item;
        });
    }

    public function getMostPopularItems(): Collection
    {
        return $this->mapItems($this->repository->getMostPopularItems());
    }

    public function mapItems(Collection $items): Collection
    {
        \Carbon\Carbon::setLocale('ka');

        $unitMapping = [
            'pcs' => ['label' => 'X',  'eng' => 'pcs'],
            'g'   => ['label' => 'გრ', 'eng' => 'g'],
            'ml'  => ['label' => 'მლ', 'eng' => 'ml'],
        ];

        return $items->transform(function ($item) use ($unitMapping) {
            return [
                'id'         => $item->getId(),
                'name'       => $item->getDisplayName(),
                'brandName'  => $item->getBrandName(),
                'image'      => $item->files->first()->url,
                'unit'       => $unitMapping[$item->unit_type],
                'unitAmount' => $item->unit,
                'prices'     => $item->prices->transform(function ($item) {
                    return [
                        'price'        => number_format($item->current_price / 100, 2),
                        'providerName' => $item->store->name,
                        'createdAt'    => $item->created_at->diffForHumans(),
                    ];
                })->toArray(),
            ];
        });
    }
}
