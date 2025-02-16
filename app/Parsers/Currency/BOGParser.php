<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;

class BOGParser extends CurrencyParser
{
    public function getData(): void
    {
        $endpoint = 'https://bankofgeorgia.ge/api/currencies/history';

        $data = collect(Http::get($endpoint)->json()['data']);

        $currencies = [
            IsoCode::USD,
            IsoCode::EUR,
            IsoCode::GBP,
        ];

        /** @var \App\Enums\Currency\IsoCode $currency */
        foreach ($currencies as $currency) {
            $rate          = $data->firstWhere('ccy', $currency->getIsoCode());
            $this->items[] = [
                'currency' => $currency,
                'buyRate'  => $rate['buyRate'],
                'sellRate' => $rate['sellRate'],
            ];
        }
    }
}
