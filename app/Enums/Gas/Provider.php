<?php

declare(strict_types=1);

namespace App\Enums\Gas;

use App\Enums\EnumTrait;
use App\Parsers\Gas\ConnectParser;
use App\Parsers\Gas\GulfParser;
use App\Parsers\Gas\LukoiliParser;
use App\Parsers\Gas\PortalParser;
use App\Parsers\Gas\RompetrolParser;
use App\Parsers\Gas\SocarParser;
use App\Parsers\Gas\WissolParser;

enum Provider: int
{
    use EnumTrait;

    case SOCAR     = 1;
    case WISSOL    = 2;
    case PORTAL    = 3;
    case GULF      = 4;
    case ROMPETROL = 5;
    case LUKOILI   = 7;
    case NEOGAS    = 8;
    case CONNECT   = 9;

    public function getData(): array
    {
        return match ($this) {
            self::SOCAR     => ['name' => 'სოკარი'],
            self::WISSOL    => ['name' => 'ვისოლი'],
            self::PORTAL    => ['name' => 'პორტალი'],
            self::GULF      => ['name' => 'გალფი'],
            self::ROMPETROL => ['name' => 'რომპეტროლი'],
            self::LUKOILI   => ['name' => 'ლოკოილი'],
            self::CONNECT   => ['name' => 'ქონექთი'],
        };
    }

    public function getParserClass(): string
    {
        return match ($this) {
            self::SOCAR     => SocarParser::class,
            self::WISSOL    => WissolParser::class,
            self::PORTAL    => PortalParser::class,
            self::GULF      => GulfParser::class,
            self::ROMPETROL => RompetrolParser::class,
            self::LUKOILI   => LukoiliParser::class,
            self::CONNECT   => ConnectParser::class,
        };
    }

    public function getImageUrl(): string
    {
        return match ($this) {
            self::SOCAR     => 'gas/providers/socar.webp',
            self::WISSOL    => 'gas/providers/wissol.webp',
            self::PORTAL    => 'gas/providers/portal.webp',
            self::GULF      => 'gas/providers/gulf.webp',
            self::ROMPETROL => 'gas/providers/rompetrol.webp',
            self::LUKOILI   => 'gas/providers/lukoili.webp',
            self::CONNECT   => 'gas/providers/connect.webp',
        };
    }
}
