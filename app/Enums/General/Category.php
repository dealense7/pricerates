<?php

declare(strict_types=1);

namespace App\Enums\General;

use App\Enums\EnumTrait;

enum Category: int
{
    use EnumTrait;

    case Alcohol    = 1;
    case NonAlcohol = 2;
    case Grocery    = 3;
    case Dairy      = 4;
    case Garden     = 5;
    case Bread      = 6;
    case Meat       = 7;

    public function getText(): string
    {
        return match ($this) {
            self::Alcohol    => 'ალკოჰოლი',
            self::NonAlcohol => 'უალკკოჰოლო',
            self::Grocery    => 'ბაკალეა',
            self::Dairy      => 'რძის ნაწარმი',
            self::Garden     => 'ბაღი',
            self::Bread      => 'პური',
            self::Meat       => 'ხორცი',
        };
    }
}
