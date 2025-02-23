<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class RompetrolParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('https://www.rompetrol.ge/');

        $crawler = new Crawler($request->getBody()->getContents());

        $result = [];

        $interesting = [
            'ევრო რეგულარი'      => 'რეგულარი',
            'efix სუპერი'        => 'სუპერი',
            'efix ევრო პრემიუმი' => 'პრემიუმი',
            'efix ევრო დიზელი'   => 'დიზელი',
            'ევრო დიზელი'        => 'დიზელი',
        ];

        $crawler
            ->filter('table tbody tr')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('td:nth-child(1)')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $name,
                        'tag'   => $interesting[$name],
                        'price' => intval((float)$node->filter('td:nth-child(2)')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }
            });

        $this->items = $result;
    }
}
