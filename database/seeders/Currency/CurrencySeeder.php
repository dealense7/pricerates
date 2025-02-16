<?php

declare(strict_types=1);

namespace Database\Seeders\Currency;

use App\Enums\Currency\IsoCode;
use App\Models\Currency\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $items = IsoCode::cases();

        $model = new Currency();
        foreach ($items as $item) {
            $model->newQuery()->updateOrCreate([
                'id' => $item->value,
            ], [
                'code' => $item->getIsoCode(),
                'name' => $item->getName(),
            ]);
        }
    }
}
