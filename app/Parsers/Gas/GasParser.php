<?php

declare(strict_types=1);

namespace App\Parsers\Gas;

use App\DTO\Gas\GasDto;
use App\Models\Gas\GasRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class GasParser implements ShouldQueue
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
            $item = new GasDto(
                name: $item['name'],
                tag: $item['tag'],
                price: $item['price'],
                date: $item['date'],
            );
        }
    }

    public function handle(): void
    {
        $this->parse($this->itemId);
    }

    private function saveData(int $providerId): void
    {
        (new GasRate())->newQuery()
            ->where('provider_id', $providerId)
            ->where('status', true)
            ->update(['status' => false]);

        foreach ($this->items as $item) {
            if (empty($item->price)) {
                continue;
            }
            (new GasRate())->newQuery()->updateOrInsert(
                [
                    'provider_id' => $providerId,
                    'name'        => $item->name,
                    'tag'         => $item->tag,
                    'date'        => $item->date,
                ],
                [
                    'price'      => $item->price,
                    'status'     => true,
                    'updated_at' => now(),
                ],
            );
        }
    }
}
