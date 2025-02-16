<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use App\Enums\Currency\IsoCode;

class CurrencyDto
{
    public function __construct(
        public IsoCode $isoCode,
        public float $buyRate,
        public float $sellRate,
    ) {
        //
    }
}
