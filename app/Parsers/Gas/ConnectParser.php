<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class ConnectParser extends GasParser
{
    public function getData(): void
    {

        $crawler = new Crawler(Browsershot::url('https://connect.com.ge/')
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->bodyHtml());

        $interesting = [
            'რეგულარი'    => 'რეგულარი',
            'პრემიუმი'    => 'პრემიუმი',
            'დიზელი'      => 'დიზელი',
            'ევრო დიზელი' => 'დიზელი',
        ];

        $result = [];

        $crawler
            ->filter('#products > div > div > div > div')
            ->each(static function (Crawler $node) use (&$result, $interesting) {
                $name = $node->filter('h2')->text();
                if (isset($interesting[$name])) {
                    $result[] = [
                        'name'  => $name,
                        'tag'   => $interesting[$name],
                        'price' => intval((float)$node->filter('p')->text() * 100),
                        'date'  => today()->format('Y-m-d'),
                    ];
                }
            });

        $this->items = $result;
    }
}
