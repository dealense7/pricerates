<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class WissolParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('https://wissol.ge/ka/fuel-prices');

        $crawler = new Crawler($request->getBody()->getContents());

        $result = [];


        $interesting = [
            'ეკო სუპერი'    => 'სუპერი',
            'ეკო პრემიუმი'  => 'პრემიუმი',
            'ეკო დიზელი'    => 'დიზელი',
            'დიზელ ენერჯი'  => 'დიზელი',
            'ევრო რეგულარი' => 'რეგულარი',
            'ვისოლ გაზი'    => 'გაზი',
        ];

        $crawler
            ->filter('.prices_wrapper > ul > li')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('span p')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $name,
                        'tag'   => $interesting[$name],
                        'price' => intval((float)$node->filter('.prices_price')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }
            });

        $this->items = $result;
    }
}
