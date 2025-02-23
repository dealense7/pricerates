<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class SocarParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('https://www.sgp.ge/ge');

        $crawler = new Crawler($request->getBody()->getContents());

        $interesting = [
            'ნანო ევრო რეგულარი' => 'რეგულარი',
            'ნანო სუპერი'        => 'სუპერი',
            'ნანო პრემიუმი'      => 'პრემიუმი',
            'ევრო 5 დიზელი'      => 'დიზელი',
            'ნანო ევრო 5 დიზელი' => 'დიზელი',
            'თხევადი გაზი (LPG)' => 'გაზი',
            'CNG ბუნებრივი აირი' => 'გაზი',
        ];

        $crawler
            ->filter('.price li')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('span')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $name,
                        'tag'   => $interesting[$name],
                        'price' => intval((float)$node->filter('div.counter')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }

            });


        $this->items = $result;
    }
}
