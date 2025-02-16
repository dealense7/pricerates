<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class KursiParser extends CurrencyParser
{
    public function getData(): void
    {
        $endpoint = 'https://api.kursi.ge/api/public/currencies';

        $data = collect(Http::get($endpoint)->json())->where('baseCurrencyCode', 'GEL');

        $this->items[] = [
            'currency' => IsoCode::USD,
            'buyRate'  => Arr::get($data->firstWhere('secondaryCurrencyCode', 'USD'), 'buyRate'),
            'sellRate' => Arr::get($data->firstWhere('secondaryCurrencyCode', 'USD'), 'sellRate'),
        ];

        $this->items[] = [
            'currency' => IsoCode::EUR,
            'buyRate'  => Arr::get($data->firstWhere('secondaryCurrencyCode', 'EUR'), 'buyRate'),
            'sellRate' => Arr::get($data->firstWhere('secondaryCurrencyCode', 'EUR'), 'sellRate'),
        ];
    }
}
