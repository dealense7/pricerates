<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Products\Item;
use Illuminate\Console\Command;

class InsertUnits extends Command
{
    protected $signature = 'app:insert-units';

    protected $description = 'Command description';

    public function handle(): void
    {
        $data = file_get_contents('/home/nebula/Documents/prices/app/Console/Commands/Parser/data.json');
        foreach (json_decode($data, true) as $unit) {
            (new Item())->query()->where('id', $unit['id'])->update([
                'unit' => $unit['unit'],
                'unit_type' => $unit['unit_type'],
                'display_name_ka' => $unit['name_ka'],
                'display_name_en' => $unit['name_en'],
                'brand_name' => $unit['brand'],
            ]);
        }
    }
}
