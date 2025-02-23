<?php

declare(strict_types=1);

namespace App\Repositories\V1\Product;

use App\Contracts\Repositories\Product\ProductRepositoryContract;
use App\Models\General\Category;
use App\Models\Products\Item;
use App\Repositories\Repository;
use App\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductRepository extends Repository implements ProductRepositoryContract
{
    public function getMostPopularItems(): Collection
    {
        $model = $this->getModel();


        $getSomeProductIds = Arr::pluck(DB::select('
            SELECT pp.item_id, COUNT(pp.id) AS count, MAX(pp.current_price) - MIN(pp.current_price) AS diff
            FROM product_prices AS pp
            JOIN product_items pi ON pi.id = pp.item_id AND pi.has_image = true
            WHERE pp.status is true
            GROUP BY pp.item_id
            HAVING count > 1
            ORDER BY count DESC, diff DESC
            LIMIT 35
        '), 'item_id');


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
            })
            ->withCount([
                'products' => function ($query) {
                    $query->where('has_image', true)->whereHas('prices', function ($query) {
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
