<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class CredoParser extends CurrencyParser
{
    public function getData(): void
    {
        $result                = [];
        $crawler               = new Crawler(
            Browsershot::url('https://credobank.ge/currency')
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->bodyHtml(),
        );
        $interestingCurrencies = [
            IsoCode::EUR->getIsoCode() => IsoCode::EUR,
            IsoCode::USD->getIsoCode() => IsoCode::USD,
            IsoCode::GBP->getIsoCode() => IsoCode::GBP,
        ];
        $crawler->filter('div.exchange-rate-component')
            ->each(static function (Crawler $node) use (&$result, $interestingCurrencies) {
                $currency = $node->filter('p.currency-description')->text();
                if (isset($interestingCurrencies[$currency])) {
                    $buyRate  = $node->filter('div.buy-sell > div:nth-child(1) > p.buy-sell-title')->text();
                    $sellRate = $node->filter('div.buy-sell > div:nth-child(2) > p.buy-sell-title')->text();
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
