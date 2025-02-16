<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\DTO\Currency\CurrencyDto;
use App\Enums\Currency\IsoCode;
use App\Models\Currency\CurrencyRate;

abstract class CurrencyParser
{
    public array $items = [];

    abstract public function getData(): void;

    public function parse(int $providerId): void
    {
        $this->getData();
        $this->transformData();
        $this->saveData($providerId);
    }

    public function transformData(): void
    {
        foreach ($this->items as &$item) {
            $item = new CurrencyDto(
                isoCode: $item['currency'],
                buyRate: (float) $item['buyRate'],
                sellRate:(float) $item['sellRate'],
            );
        }
    }

    protected function getFakeData(): void
    {
        $this->items = [
            [
                'currency' => IsoCode::USD,
                'buyRate'  => 2.74,
                'sellRate' => 2.84,
            ],
            [
                'currency' => IsoCode::EUR,
                'buyRate'  => 2.837,
                'sellRate' => 2.965,
            ],
            [
                'currency' => IsoCode::GBP,
                'buyRate'  => 3.417,
                'sellRate' => 3.575,
            ],
        ];
    }

    private function saveData(int $providerId): void
    {
        $date = now()->format('Y-m-d');
        $items = collect($this->items)->transform(static function ($item) use ($providerId, $date) {
            return [
                'provider_id' => $providerId,
                'currency_id' => $item->isoCode->value,
                'buy_rate'    => number_format($item->buyRate, 3),
                'sell_rate'   => number_format($item->sellRate, 3),
                'date'        => $date,
            ];
        })->toArray();

        (new CurrencyRate())->newQuery()->insert($items);
    }
}
