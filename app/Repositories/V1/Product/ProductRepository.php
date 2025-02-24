<?php

declare(strict_types=1);

namespace App\Repositories\V1\Product;

use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Models\General\Category;
use App\Models\Products\Item;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductRepository extends Repository implements ProductRepositoryContract
{
    public function getItems(array $filters = []): LengthAwarePaginator
    {
        $model = $this->getModel();

        $query = $model->newQuery()
            ->select('product_items.*')
            ->join('product_prices as pp', 'pp.item_id', '=', 'product_items.id')
            ->where('product_items.has_image', true)
            ->where('pp.status', true)
            ->groupBy('product_items.id')
            ->havingRaw('COUNT(pp.id) > 1')
            ->when(Arr::get($filters, 'categoryId'), function ($query, $filter) {
                $query->where('product_items.category_id', (int) $filter);
            })
            ->orderByRaw('COUNT(pp.id) DESC, MAX(pp.current_price) - MIN(pp.current_price) DESC')
            ->with([
                'files',
                'prices' => static function ($query) {
                    $query->with('store')->where('status', true)->orderBy('current_price', 'asc');
                },
            ]);

        return $query->paginate(30);
    }

    public function getMostPopularItems(): Collection
    {
        $model = $this->getModel();


        $getSomeProductIds = DB::table('product_prices AS pp')
            ->join('product_items AS pi', function ($join) {
                $join->on('pi.id', '=', 'pp.item_id')
                    ->where('pi.has_image', true);
            })
            ->where('pp.status', true)
            ->groupBy('pp.item_id')
            ->havingRaw('COUNT(pp.id) > 1')
            ->orderByRaw('COUNT(pp.id) DESC, MAX(pp.current_price) - MIN(pp.current_price) DESC')
            ->limit(35)
            ->pluck('pp.item_id');


        $query = $model->newQuery()
            ->with([
                'files',
                'prices' => static function ($query) {
                    $query->with('store')->where('status', true)->orderBy('current_price', 'asc');
                },
            ])
            ->whereIn('id', $getSomeProductIds);

        /** @var \App\Support\Collection $items */
        $items = $query->get();

        return $items;
    }

    public function getRandomCategoryItems(): Collection
    {
        $model = (new Category());


        $query = $model->newQuery()
            ->whereHas('products', function ($query) {
                $query->where('has_image', true)->whereHas('prices', function ($query) {
                    $query->where('status', true);
                });
            }, '>=', 7)
            ->withCount([
                'products' => function ($query) {
                    $query->where('has_image', true)
                        ->whereHas('prices', function ($query) {
                            $query->where('status', true);
                        });
                }
            ])
            ->with([
                'products' => function ($query) {
                    $query->select('product_items.*')
                        ->join('product_prices as pp', 'pp.item_id', '=', 'product_items.id')
                        ->where('product_items.has_image', true)
                        ->where('pp.status', true)
                        ->groupBy('product_items.id')
                        ->havingRaw('COUNT(pp.id) > 1')
                        ->orderByRaw('COUNT(pp.id) DESC, MAX(pp.current_price) - MIN(pp.current_price) DESC')
                        ->with([
                            'files',
                            'prices' => static function ($query) {
                                $query->with('store')->where('status', true)->orderBy('current_price', 'asc');
                            },
                        ])
                        ->limit(10);
                }
            ]);

        /** @var \App\Support\Collection $items */
        $items = $query->get();

        return $items;
    }

    public function getModel(): Item
    {
        return new Item();
    }
}
