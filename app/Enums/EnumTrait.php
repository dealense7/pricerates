<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ValueError;

use function implode;

/** @method static cases() */
trait EnumTrait
{
    public static function fromName(string $name): self
    {
        foreach (self::cases() as $case) {
            if ($name === $case->name) {
                return $case;
            }
        }
        throw new ValueError("$name is not a valid backing value for enum " . self::class);
    }

    public static function values(): array
    {
        return self::toCollection()->pluck('value')->toArray();
    }

    public static function options(): array
    {
        return self::toCollection()->pluck('name', 'value')->map(static fn ($option) => Str::headline($option))->toArray();
    }

    public static function names(): array
    {
        return self::toCollection()->pluck('name')->toArray();
    }

    public static function implodedValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

    public static function implodedNames(string $separator = ','): string
    {
        return implode($separator, self::names());
    }

    public static function randomValue()
    {
        return Arr::random(self::values());
    }

    public static function toCollection(): Collection
    {
        return collect(self::cases());
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
