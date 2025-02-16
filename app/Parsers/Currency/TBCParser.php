<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;

class TBCParser extends CurrencyParser
{
    public function getData(): void
    {
        $urls = [
            [
                'iso' => IsoCode::EUR,
                'url' => 'https://apigw.tbc.ge/api/v1/exchangeRates/getExchangeRate?Iso1=EUR&Iso2=GEL',
            ],
            [
                'iso' => IsoCode::USD,
                'url' => 'https://apigw.tbc.ge/api/v1/exchangeRates/getExchangeRate?Iso1=USD&Iso2=GEL',
            ],
            [
                'iso' => IsoCode::GBP,
                'url' => 'https://apigw.tbc.ge/api/v1/exchangeRates/getExchangeRate?Iso1=GBP&Iso2=GEL',
            ],
        ];

        foreach ($urls as $url) {
            $data          = Http::get($url['url'])->json();
            $this->items[] = [
                'currency' => $url['iso'],
                'buyRate'  => $data['buyRate'],
                'sellRate' => $data['sellRate'],
            ];
        }
    }
}
