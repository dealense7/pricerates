<?php

namespace App\Parsers;

use App\Enums\General\Category;
use App\Models\Products\Item;
use App\Models\Products\Price;
use Illuminate\Support\Facades\DB;

abstract class Parser
{
    public int|null $storeId = null;

    protected abstract function parse(...$args): void;

    protected abstract function getName(array $item): string;

    protected abstract function getPrice(array $item): int;

    protected abstract function getProviderId(): int;

    protected abstract function setStoreId(int $storeId): void;

    protected abstract function getBarCode(array $item): string;

    protected abstract function getBarCodeFromName(array $item): string;

    protected abstract function getBarCodeFromImage(array $item): ?string;

    /**
     * @param array $items
     * @return array<ItemDto>
     */
    public function formatItems(array $items, Category $category): array
    {
        $data = [];
        foreach ($items as $item) {
            $barCode = $this->getBarCode($item);
            if (!$this->validateBarCode(strlen($barCode))) {
                $barCode = $this->getBarCodeFromName($item);
                if (!$this->validateBarCode(strlen($barCode))) {
                    $barCode = $this->getBarCodeFromImage($item);
                    if (!$this->validateBarCode(strlen($barCode))) {
                        continue;
                    }
                }
            }

            $data[] = new ItemDto(
                $this->getName($item),
                $barCode,
                $this->getPrice($item),
                $this->storeId,
                $this->getProviderId(),
                $category->value
            );
        }

        return $data;
    }

    /**
     * @param array<ItemDto> $items
     */
    public function storeItems(array $items): void
    {
        /** @var ItemDto $item */
        foreach ($items as $item) {
            $productId = $this->getItemIdByBarCode($item->barCode);
            if (is_null($productId)) {
                $productId = $this->createItemGetId($item);
            }
            $this->savePrice($productId, $item);
        }
    }

    private function validateBarCode(int $len): bool
    {
        //  EAN-8 → 8 digits (Used for small products like cans, chocolates)
        //  EAN-13 → 13 digits (Used internationally on most products)
        //  GTIN-14 → 14 digits (For Product packaging and cases)

        if (in_array($len, [8, 13, 14], true)) {
            return true;
        }
        return false;
    }

    private function getItemIdByBarCode(string $barCode): ?int
    {
        return DB::table((new Item())->getTable())->where('barcode', $barCode)->select('id')->first()?->id;
    }

    private function createItemGetId(ItemDto $item): int
    {
        $data = [
            'name'        => $item->name,
            'slug'        => 'slug',
            'barcode'     => $item->barCode,
            'category_id' => $item->categoryId,
            //                'category_id',
            //                'show',
        ];

        return DB::table((new Item())->getTable())->insertGetId($data);
    }

    private function savePrice(int $productId, ItemDto $item): void
    {
        $data = [
            'item_id'       => $productId,
            'provider_id'   => $item->providerId,
            'store_id'      => $item->storeId,
            'current_price' => $item->price,
            'created_at'    => now()->format('Y-m-d H')
        ];

        DB::table((new Price())->getTable())->updateOrInsert(
            [
                'item_id'     => $productId,
                'provider_id' => $item->providerId,
                'store_id'    => $item->storeId,
                'created_at'  => now()->format('Y-m-d H')
            ],
            $data
        );
    }
}
