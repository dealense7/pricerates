<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class SwissParser extends CurrencyParser
{
    public function getData(): void
    {
        $request = Http::get('https://swisscapital.ge/ge/currency');

        $result = [];

        $crawler               = new Crawler($request->getBody()->getContents());

        $crawler
            ->filter('body > div.global-wrap > header.product-full-top > div.calc-wrapper > div > div.calc.calc-currency.calc-currency-left.curr-list > div.cl-type.cl-type-sc > table > tbody > tr')
            ->each(static function (Crawler $node) use (&$result) {
                $code = $node->filter('td:nth-child(1) > span.cl-code')->text();
                if (Str::contains($code, ['USD', 'EUR', 'GBP'])) {
                    $currency = IsoCode::USD;
                    if (Str::contains($code, 'EUR')) {
                        $currency = IsoCode::EUR;
                    }
                    if (Str::contains($code, 'GBP')) {
                        $currency = IsoCode::GBP;
                    }
                    $result[] = [
                        'currency' => $currency,
                        'buyRate'  => (float) $node->filter('td:nth-child(2)')->text(),
                        'sellRate' => (float) $node->filter('td:nth-child(3)')->text(),
                    ];
                }
            });
        $this->items = $result;
    }
}
