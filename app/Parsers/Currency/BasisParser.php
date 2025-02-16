<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;

class BasisParser extends CurrencyParser
{
    public function getData(): void
    {
        $endpoint = 'https://static.bb.ge/source/api/view/main/getXrates';

        $response = Http::get($endpoint)->json();
        $rates    = json_decode($response[0]['xrates'], true);

        $buyCourse  = $rates['kursBuy'];
        $sellCourse = $rates['kursSell'];

        $interestingCurrencies = [
            IsoCode::EUR->getIsoCode() => IsoCode::EUR,
            IsoCode::USD->getIsoCode() => IsoCode::USD,
            IsoCode::GBP->getIsoCode() => IsoCode::GBP,
        ];
        $result                = [];

        foreach ($buyCourse as $key => $value) {
            if (isset($interestingCurrencies[$key])) {
                $result[$key] = [
                    'currency' => $interestingCurrencies[strtoupper($key)],
                    'buyRate'  => $value,
                    'sellRate' => null,
                ];
            }
        }

        foreach ($sellCourse as $key => $value) {
            if (isset($interestingCurrencies[$key])) {
                $result[$key]['sellRate'] = $value;
            }
        }

        $this->items = $result;
    }
}
