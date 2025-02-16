<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class LibertyParser extends CurrencyParser
{
    public function getData(): void
    {
        $request = Http::get('https://libertybank.ge/en/');

        $result = [];

        $crawler               = new Crawler($request->getBody()->getContents());
        $interestingCurrencies = [
            IsoCode::EUR->getIsoCode() => IsoCode::EUR,
            IsoCode::USD->getIsoCode() => IsoCode::USD,
            IsoCode::GBP->getIsoCode() => IsoCode::GBP,
        ];

        $crawler
            ->filter('#currencyrates1 > div.currency-rates__body > div.currency-rates__row-body > div.currency-rates__preview > div.js-homepage__currency-item')
            ->each(static function (Crawler $node) use (&$result, $interestingCurrencies) {
                $currency = $node
                    ->filter('div:nth-child(1) > div.currency-rates__currency-name > span')
                    ->text();
                if (isset($interestingCurrencies[$currency])) {
                    $buyRate  = $node
                        ->filter('div:nth-child(3) > span:nth-child(1)')
                        ->text();
                    $sellRate = $node
                        ->filter('div:nth-child(3) > span:nth-child(2)')
                        ->text();
                    $result[] = [
                        'currency' => $interestingCurrencies[strtoupper($currency)],
                        'buyRate'  => (float) $buyRate,
                        'sellRate' => (float) $sellRate,
                    ];
                }
            });
        $this->items = $result;
    }
}
