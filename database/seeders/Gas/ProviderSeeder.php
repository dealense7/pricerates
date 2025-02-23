<?php

declare(strict_types=1);

namespace Database\Seeders\Gas;

use App\Enums\Gas\Provider;
use App\Models\Gas\Provider as GasProvider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $items = Provider::cases();

        $model = new GasProvider();

        foreach ($items as $item) {
            $model->newQuery()->updateOrCreate(
                ['id' => $item->value],
                [
                    'logo_url' => $item->getImageUrl(),
                    ...$item->getData(),
                ],
            );
        }
    }
}
