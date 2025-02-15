<?php

declare(strict_types=1);

namespace Database\Seeders\General;

use App\Enums\General\Provider;
use App\Models\General\Provider as ProviderModel;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'id'     => Provider::Glovo->value,
                'name'   => 'Glovo',
                'status' => true,
            ],
        ];

        foreach ($items as $item) {
            (new ProviderModel())->create($item);
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([ProviderModel::class])->flush();
        }
    }
}
