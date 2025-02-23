<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Currency\Provider;
use App\Models\Currency\CurrencyRate;
use App\Models\Currency\Provider as CurrencyProvider;
use Illuminate\Cache\TaggableStore;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchCurrencyData extends Command
{
    protected $signature = 'app:fetch-currency-data';
    protected $description = 'Fetch currency data';

    public function handle(): void
    {
        $items = (new CurrencyProvider())->newQuery()->get();

        /** @var \App\Enums\Currency\Provider $item */
        foreach ($items as $item) {
            if (! $item->getStatus()) {
                continue;
            }
            /** @var \App\Parsers\Currency\CurrencyParser $parser */
            $parser = Provider::from($item->getId())->getParserClass();

            $parser::dispatch($item->getId());
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([CurrencyRate::class])->flush();
        }
    }
}
