<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class PortalParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('https://portal.com.ge/georgian/newfuel');

        $crawler = new Crawler($request->getBody()->getContents());

        $result = [];


        $interesting = [
            'SUPER'         => [
                'name' => 'სუპერი',
                'tag'  => 'სუპერი',
            ],
            'PREMIUM'       => [
                'name' => 'პრემიუმი',
                'tag'  => 'პრემიუმი',
            ],
            'EURO REGULAR'  => [
                'name' => 'ევრო რეგულარი',
                'tag'  => 'რეგულარი',
            ],
            'EURO DIESEL'   => [
                'name' => 'ევრო დიზელი',
                'tag'  => 'დიზელი',
            ],
            'EFFECT DIESEL' => [
                'name' => 'ეფექტ დიზელი',
                'tag'  => 'დიზელი',
            ],
        ];

        $crawler
            ->filter('body > section > div.content_div > div > div')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('h3')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $interesting[$name]['name'],
                        'tag'   => $interesting[$name]['tag'],
                        'price' => intval((float)$node->filter('.fuel_price')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }
            });

        $this->items = $result;
    }
}
