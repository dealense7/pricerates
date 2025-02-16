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
    public null | int $storeId = null;

    /**
     * @param array $items
     * @return array<\App\Parsers\ItemDto>
     */
    public function formatItems(array $items, Category $category): array
    {
        $data = [];
        foreach ($items as $item) {
            $barCode = $this->getBarCode($item);
            if (! $this->validateBarCode(strlen($barCode))) {
                $barCode = $this->getBarCodeFromName($item);
                if (! $this->validateBarCode(strlen($barCode))) {
                    $barCode = $this->getBarCodeFromImage($item);
                    if (! $this->validateBarCode(strlen($barCode))) {
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
            if (! is_null($productId) && $item->imageUrl) {
                $imageUrl = $item->imageUrl;
                $response = Http::get($imageUrl);

                if ($response->successful()) {
                    // Get the imgs content from the response
                    $imageContents = $response->body();

                    // Generate a unique filename for the imgs

                    $filename = basename($imageUrl);
                    $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                    $filename = $item->barCode . '.' . $fileExtension;

                    // Store the imgs locally (use 'public' disk for public accessibility or 'local' for private storage)
                    Storage::disk('public')->put('images/' . $filename, $imageContents);

                        // Prepare the file data for the database
                        $data = [
                            'url' => Storage::url('images/' . $filename), // Generate the URL for the saved file
                            'disk' => 'public',
                            'extension' => $fileExtension,
                            'size' => round(filesize(Storage::disk('public')->path('images/' . $filename)) / 1024, 2),
                            'fileable_type' => Item::class,
                            'fileable_id' => $productId,
                        ];

                        (new File())->newQuery()->insert($data);
                        DB::table((new Item())->getTable())->where('id', $productId)->update(['has_image' => true]);
                }
            }
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
        return DB::table((new Item())->getTable())->where('has_image', false)->whereNotNull('display_name_ka')->where('barcode', $barCode)->select('id')->first()?->id;
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
            'created_at'    => now()->format('Y-m-d H'),
        ];

        DB::table((new Price())->getTable())->updateOrInsert(
            [
                'item_id'     => $productId,
                'provider_id' => $item->providerId,
                'store_id'    => $item->storeId,
                'created_at'  => now()->format('Y-m-d H'),
            ],
            $data,
        );
    }
}
