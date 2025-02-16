<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\Category;
use App\Models\General\Category as CategoryModel;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = Category::toCollection();

        /** @var \App\Enums\General\Category $item */
        foreach ($items as $item) {
            CategoryModel::query()->updateOrInsert(
                [
                    'id' => $item->value,
                ],
                [
                    'name' => $item->getText(),
                    'slug' => strtolower($item->name),
                ],
            );
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([CategoryModel::class])->flush();
        }
    }
}
