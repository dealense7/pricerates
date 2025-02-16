<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class RicoParser extends CurrencyParser
{
    public function getData(): void
    {
        $request = Http::get('https://www.rico.ge/ka');

        $result = [];

        $crawler               = new Crawler($request->getBody()->getContents());
        $interestingCurrencies = [
            IsoCode::EUR->getIsoCode() => IsoCode::EUR,
            IsoCode::USD->getIsoCode() => IsoCode::USD,
            IsoCode::GBP->getIsoCode() => IsoCode::GBP,
        ];

        $crawler
            ->filter('body > main > div > section.calculators-section > div > section > div.currencies > div.currencies-table > div > table > tbody')
            ->each(static function (Crawler $node) use (&$result, $interestingCurrencies) {
                $node
                    ->filter('tr')
                    ->each(static function (Crawler $node) use (&$result, $interestingCurrencies) {
                        [$currency, $buy, $sell] = explode(' ', $node->text(), 3);

                        if (isset($interestingCurrencies[strtoupper($currency)])) {
                            $result[] = [
                                'currency' => $interestingCurrencies[strtoupper($currency)],
                                'buyRate'  => $buy,
                                'sellRate' => $sell,
                            ];
                        }
                    });
            });
        $this->items = $result;
    }
}
