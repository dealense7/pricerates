<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Currency\Provider;
use App\Models\Currency\Provider as CurrencyProvider;
use Illuminate\Console\Command;

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
            $parser = resolve(Provider::from($item->getId())->getParserClass());

            $parser->parse($item->getId());
        }
    }
}
