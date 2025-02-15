<?php

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
    )
    {
        //
    }
}
