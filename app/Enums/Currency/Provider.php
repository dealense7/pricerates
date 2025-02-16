<?php

declare(strict_types=1);

namespace App\Enums\Currency;

use App\Enums\EnumTrait;
use App\Parsers\Currency\BasisParser;
use App\Parsers\Currency\BOGParser;
use App\Parsers\Currency\CredoParser;
use App\Parsers\Currency\CrystalParser;
use App\Parsers\Currency\KursiParser;
use App\Parsers\Currency\LibertyParser;
use App\Parsers\Currency\ProCreditParser;
use App\Parsers\Currency\RicoParser;
use App\Parsers\Currency\SwissParser;
use App\Parsers\Currency\TBCParser;
use App\Parsers\Currency\TeraParser;

enum Provider: int
{
    use EnumTrait;

    case TBC           = 1;
    case BOG           = 2;
    case LIBERTY       = 3;
    case RICO_CREDIT   = 4;
    case CRYSTAL       = 5;
    case CREDO_BANK    = 6;
    case BASIS         = 7;
    case PRO_CREDIT    = 9;
    case TERA_BANK     = 10;
    case SWISS_CAPITAL = 11;
    case KURSI         = 12;

    public function getData(): array
    {
        return match ($this) {
            self::TBC           => ['title' => 'TBC', 'name' => 'თიბისი'],
            self::BOG           => ['title' => 'BOG', 'name' => 'საქ.ბანკი'],
            self::LIBERTY       => ['title' => 'LIB', 'name' => 'ლიბერთი'],
            self::RICO_CREDIT   => ['title' => 'ROC', 'name' => 'რიკო'],
            self::CRYSTAL       => ['title' => 'CRY', 'name' => 'კრისტალი'],
            self::CREDO_BANK    => ['title' => 'CRE', 'name' => 'კრედო'],
            self::BASIS         => ['title' => 'BAS', 'name' => 'ბაზისი'],
            self::PRO_CREDIT    => ['title' => 'PCR', 'name' => 'პრო კრედიტ'],
            self::TERA_BANK     => ['title' => 'TER', 'name' => 'ტერა'],
            self::SWISS_CAPITAL => ['title' => 'SWS', 'name' => 'სვის კაპიტ'],
            self::KURSI         => ['title' => 'KUR', 'name' => 'კურსი'],
        };
    }

    public function getParserClass(): string
    {
        return match ($this) {
            self::TBC           => TBCParser::class,
            self::BOG           => BOGParser::class,
            self::LIBERTY       => LibertyParser::class,
            self::RICO_CREDIT   => RicoParser::class,
            self::CRYSTAL       => CrystalParser::class,
            self::CREDO_BANK    => CredoParser::class,
            self::BASIS         => BasisParser::class,
            self::PRO_CREDIT    => ProCreditParser::class,
            self::TERA_BANK     => TeraParser::class,
            self::SWISS_CAPITAL => SwissParser::class,
            self::KURSI         => KursiParser::class,
        };
    }

    public function getImageUrl(): string
    {
        return match ($this) {
            self::TBC           => 'currency/providers/tbc.png',
            self::BOG           => 'currency/providers/bog.png',
            self::LIBERTY       => 'currency/providers/liberty.png',
            self::RICO_CREDIT   => 'currency/providers/rico.png',
            self::CRYSTAL       => 'currency/providers/crystal.png',
            self::CREDO_BANK    => 'currency/providers/credo.png',
            self::BASIS         => 'currency/providers/basis.png',
            self::PRO_CREDIT    => 'currency/providers/pro-credit.png',
            self::TERA_BANK     => 'currency/providers/tera.png',
            self::SWISS_CAPITAL => 'currency/providers/swiss-capital.png',
            self::KURSI         => 'currency/providers/kursi.png',
        };
    }
}
