<?php

declare(strict_types=1);

namespace App\Parsers\Currency;

use App\DTO\Currency\CurrencyDto;
use App\Enums\Currency\IsoCode;
use App\Models\Currency\CurrencyRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CurrencyParser implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    public array $items = [];

    public function __construct(public int $itemId)
    {
    }

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

    public function handle(): void
    {
        $this->parse($this->itemId);
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

        foreach ($this->items as $item) {
            (new CurrencyRate())->newQuery()->updateOrInsert(
                [
                    'provider_id' => $providerId,
                    'currency_id' => $item->isoCode->value,
                    'date'        => $date,
                ],
                [
                    'buy_rate'  => number_format($item->buyRate, 3),
                    'sell_rate' => number_format($item->sellRate, 3),
                ],
            );
        }

        (new CurrencyRate())->newQuery()
            ->where('status', false)
            ->where('provider_id', $providerId)
            ->where('date', '>=', today()->startOfDay())
            ->update(['status' => true]);

        (new CurrencyRate())->newQuery()
            ->where('status', true)
            ->where('provider_id', $providerId)
            ->where('date', '<', today()->startOfDay())
            ->update(['status' => false]);

    }
}
