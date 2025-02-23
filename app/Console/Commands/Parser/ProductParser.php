<?php

declare(strict_types=1);

namespace App\Console\Commands\Parser;

use App\Models\Products\Price;
use App\Models\Store\Store;
use App\Parsers\GlovoParser;
use Illuminate\Cache\TaggableStore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProductParser extends Command
{
    protected $signature   = 'app:product-parser';
    protected $description = 'Fetch new products and it prices';

    public function handle(): void
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

        (new Price())->newQuery()
            ->where('status', false)
            ->where('created_at', '>=', today()->startOfDay())
            ->update(['status' => true]);

        (new Price())->newQuery()
            ->where('status', true)
            ->where('created_at', '<', now()->subDays(3))
            ->update(['status' => false]);



        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([Price::class])->flush();
        }
    }
}
