<?php

declare(strict_types=1);

namespace App\DTO\Gas;

class GasDto
{
    public function __construct(
        public string $name,
        public string $tag,
        public int $price,
        public string $date,
    ) {
        //
    }
}
