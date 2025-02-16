<?php

declare(strict_types=1);

namespace App\Enums\Currency;

use App\Enums\EnumTrait;

enum IsoCode: int
{
    use EnumTrait;

    case GEL = 1;
    case USD = 2;
    case GBP = 3;
    case EUR = 4;

    public function getIsoCode(): string
    {
        return match ($this) {
            self::GEL => 'GEL',
            self::USD => 'USD',
            self::GBP => 'GBP',
            self::EUR => 'EUR',
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::GEL => 'ლარი',
            self::USD => 'დოლარი',
            self::GBP => 'გირვანქა სტერლინგი',
            self::EUR => 'ევრო',
        };
    }

    public function getSymbol(): string
    {
        return match ($this) {
            self::GEL => '₾',
            self::USD => '$',
            self::GBP => '£',
            self::EUR => '€',
        };
    }
}
