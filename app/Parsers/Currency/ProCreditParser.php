<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ProCreditParser extends CurrencyParser
{
    public function getData(): void
    {
        $request = Http::get('https://www.procreditbank.ge/en/exchange');

        $result = [];

        $crawler               = new Crawler($request->getBody()->getContents());

        $crawler
            ->filter('#block-system-main > div > div > section > div > div > div.exchange-oficial-rates-bl > div.exchange-items > article.exchange-item')
            ->each(static function (Crawler $node) use (&$result) {
                $imageUrl = $node->filter('div.exchange-img > img')->attr('src');
                if (Str::contains($imageUrl, ['usa', 'euro', 'eng'])) {
                    $currency = IsoCode::USD;
                    if (Str::contains($imageUrl, 'euro')) {
                        $currency = IsoCode::EUR;
                    }
                    if (Str::contains($imageUrl, 'eng')) {
                        $currency = IsoCode::GBP;
                    }
                    $result[] = [
                        'currency' => $currency,
                        'buyRate'  => (float) $node->filter('div.exchange-buy')->text(),
                        'sellRate' => (float) $node->filter('div.exchange-sell')->text(),
                    ];
                }
            });

        $this->items = $result;
    }
}
