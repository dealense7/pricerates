<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\Enums\Currency\IsoCode;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class CrystalParser extends CurrencyParser
{
    public function getData(): void
    {
        $result  = [];
        $crawler = new Crawler(
            Browsershot::url('https://crystal.ge/valutis-kursebi/')
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->bodyHtml(),
        );

        $crawler->filterXPath("//div[@id='currencyData']/div")
            ->each(function (Crawler $node) use (&$result) {
                [$amountWithCurrency, $official, $buy, $sell] = explode(' ', $node->text(), 4);
                [$amount, $currency] = preg_split('/(?<=\d)(?=\D)/', $amountWithCurrency);
                $this->mapCurrency($currency);
                if (! is_null($currency)) {
                    $result[] = [
                        'currency' => $currency,
                        'buyRate'  => $buy / (int) $amount,
                        'sellRate' => $sell / (int) $amount,
                    ];
                }
            });
        $this->items = $result;
    }

    private function mapCurrency(string &$currency): void
    {
        $currency = match ($currency) {
            'დოლარი' => IsoCode::USD,
            'ევრო'   => IsoCode::EUR,
            'ფუნტი'  => IsoCode::GBP,
            default  => null
        };
    }
}
