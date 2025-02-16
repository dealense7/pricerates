<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;

class TeraParser extends CurrencyParser
{
    public function getData(): void
    {
        $endpoint = 'https://terabank.ge/_mvcapi/CurrencyRatesApi/GetTeraCrossRates';

        $data  = Http::post($endpoint)->json();
        $items = $data['data'];

        $interestingCurrencies = [
            IsoCode::EUR->getIsoCode() => IsoCode::EUR,
            IsoCode::USD->getIsoCode() => IsoCode::USD,
            IsoCode::GBP->getIsoCode() => IsoCode::GBP,
        ];
        foreach ($items as $item) {
            if (isset($interestingCurrencies[$item['iso']])) {
                $this->items[] = [
                    'currency' => $interestingCurrencies[$item['iso']],
                    'buyRate'  => $item['teraCrossRateBuy'],
                    'sellRate' => $item['teraCrossRateSell'],
                ];
            }
        }
    }
}
