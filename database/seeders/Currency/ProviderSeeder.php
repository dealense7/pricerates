<?php

declare(strict_types=1);

namespace Database\Seeders\Currency;

use App\Enums\Currency\Provider;
use App\Models\Currency\Provider as CurrencyProvider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $items = Provider::cases();

        $model = new CurrencyProvider();

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
