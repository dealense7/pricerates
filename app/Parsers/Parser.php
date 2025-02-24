<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Enums\General\Category;
use App\Models\General\File;
use App\Models\Products\Item;
use App\Models\Products\Price;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

abstract class Parser
{
    public null|int $storeId = null;

    /**
     * @param array $items
     * @return array<\App\Parsers\ItemDto>
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
                    if (is_null($barCode) || !$this->validateBarCode(strlen($barCode))) {
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
                $category->value,
                $this->getImageUrl($item),
            );
        }

        return $data;
    }

    /**
     * @param array<\App\Parsers\ItemDto> $items
     */
    public function storeItems(array $items): void
    {
        /** @var \App\Parsers\ItemDto $item */
        foreach ($items as $item) {
            $productId = $this->getItemIdByBarCode($item->barCode);
            if (is_null($productId)) {
                $productId = $this->createItemGetId($item);
            }
            $this->savePrice($productId, $item);
        }
    }

    abstract protected function parse(...$args): void;

    abstract protected function getName(array $item): string;

    abstract protected function getPrice(array $item): int;

    abstract protected function getProviderId(): int;

    abstract protected function setStoreId(int $storeId): void;

    abstract protected function getBarCode(array $item): string;

    abstract protected function getBarCodeFromName(array $item): string;

    abstract protected function getBarCodeFromImage(array $item): ?string;

    private function validateBarCode(?int $len = null): bool
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
        //        return DB::table((new Item())->getTable())->where('has_image', false)->whereNotNull('display_name_ka')->where('barcode', $barCode)->select('id')->first()?->id;
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
        (new Price())->newQuery()
            ->where('item_id', $productId)
            ->where('status', true)
            ->where('provider_id', $item->providerId)
            ->update(['status' => false]);

        $currentHour = now()->startOfHour()->format('Y-m-d H');

        (new Price())->newQuery()->updateOrInsert(
            [
                'item_id'     => $productId,
                'provider_id' => $item->providerId,
                'store_id'    => $item->storeId,
                'created_at'  => $currentHour,
            ],
            [
                'current_price' => $item->price,
                'status'      => true,
            ],
        );
    }
}
