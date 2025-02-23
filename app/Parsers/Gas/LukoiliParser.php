<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class LukoiliParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('lukoil.ge');

        $crawler = new Crawler($request->getBody()->getContents());

        $result = [];


        $interesting = [
            'Super Ecto 100'   => [
                'name' => 'სუპერ ექტო 100',
                'tag'  => 'სუპერი'
            ],
            'Euro Regular'     => [
                'name' => 'ევრო რეგულარი',
                'tag'  => 'რეგულარი'
            ],
            'Super Ecto'       => [
                'name' => 'სუპერ ექტო',
                'tag'  => 'სუპერი'
            ],
            'Premium Avangard' => [
                'name' => 'პრემიუმ ავანგარდი',
                'tag'  => 'პრემიუმი'
            ],
            'Euro Diesel'      => [
                'name' => 'ევრო დიზელი',
                'tag'  => 'დიზელი'
            ],
        ];

        $crawler
            ->filter('body > div.w-screen > div.lg\:grid span')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('div:nth-child(2) p:nth-child(2)')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $interesting[$name]['name'],
                        'tag'   => $interesting[$name]['tag'],
                        'price' => intval((float)$node->filter('div:nth-child(2) p:nth-child(1)')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }
            });

        $this->items = $result;
    }
}
