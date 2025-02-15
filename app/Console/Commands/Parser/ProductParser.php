<?php

declare(strict_types=1);

namespace App\Console\Commands\Parser;

use App\Models\Store\Store;
use App\Parsers\GlovoParser;
use Illuminate\Console\Command;

class ProductParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:product-parser';

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
        $stores = (new Store())->query()
            ->with(['urls'])
            ->where('show', true)->get();

        foreach ($stores as $store) {
            foreach ($store->urls as $url) {
                (new GlovoParser())->parse(
                    url: $url->meta['url'],
                    apiBase: $url->meta['api_base'],
                    categories: $url->meta['categories'],
                    storeId: $store->id,
                );
            }
        }
    }
}
