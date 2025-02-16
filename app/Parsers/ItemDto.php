<?php

declare(strict_types=1);

namespace App\Parsers;

class ItemDto
{
    public function __construct(
        public string $name,
        public string $barCode,
        public int $price,
        public int $storeId,
        public int $providerId,
        public int $categoryId,
        public ?string $imageUrl = null,
    ) {
        //
    }
}
