<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class GulfParser extends GasParser
{
    public function getData(): void
    {
        $request = Http::get('https://gulf.ge/');

        $crawler = new Crawler($request->getBody()->getContents());

        $result = [];

        $interesting = [
            'ევრო რეგულარი'         => 'რეგულარი',
            'G-Force ევრო რეგულარი' => 'რეგულარი',
            'G-Force სუპერი'        => 'სუპერი',
            'G-Force პრემიუმი'      => 'პრემიუმი',
            'G-Force ევრო დიზელი'   => 'დიზელი',
            'ევრო დიზელი'           => 'დიზელი',
            'გაზი'                  => 'გაზი',
        ];

        $crawler
            ->filter('.price_entry')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('.product_name')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $name,
                        'tag'   => $interesting[$name],
                        'price' => intval((float)$node->filter('.product_price')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }

            });

        $this->items = $result;
    }
}
