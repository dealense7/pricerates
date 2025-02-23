<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Gas\Provider;
use App\Models\Gas\Provider as GasProvider;
use Illuminate\Console\Command;

class FetchGasData extends Command
{
    protected $signature = 'app:fetch-gas-data';
    protected $description = 'Fetch Gas data';

    public function handle(): void
    {
        $items = (new GasProvider())->newQuery()->get();

        /** @var \App\Models\Gas\Provider $item */
        foreach ($items as $item) {
            if (! $item->getStatus()) {
                continue;
            }
            /** @var \App\Parsers\Gas\GasParser $parser */
            $parser = Provider::from($item->getId())->getParserClass();

            $parser::dispatch($item->getId());
        }
    }
}
