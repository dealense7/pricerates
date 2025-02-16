<?php

namespace App\Console\Commands;

use App\Models\Products\Item;
use Illuminate\Console\Command;

class insertUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-units';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
