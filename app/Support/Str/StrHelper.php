<?php

declare(strict_types=1);

namespace App\Support\Str;

class StrHelper
{
    public const UUID_VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    public static $geoLatMapper = [
        'ა' => 'a',
        'ბ' => 'b',
        'გ' => 'g',
        'დ' => 'd',
        'ე' => 'e',
        'ვ' => 'v',
        'ზ' => 'z',
        'თ' => 't',
        'ი' => 'i',
        'კ' => 'k',
        'ლ' => 'l',
        'მ' => 'm',
        'ნ' => 'n',
        'ო' => 'o',
        'პ' => 'p',
        'ჟ' => 'zh',
        'რ' => 'r',
        'ს' => 's',
        'ტ' => 't',
        'უ' => 'u',
        'ფ' => 'f',
        'ქ' => 'q',
        'ღ' => 'gh',
        'ყ' => 'y',
        'შ' => 'sh',
        'ჩ' => 'ch',
        'ც' => 'ts',
        'ძ' => 'dz',
        'წ' => 'w',
        'ჭ' => 'ch',
        'ხ' => 'x',
        'ჯ' => 'j',
        'ჰ' => 'h',
    ];

    /**
     * This function converts georgian letters to latin.
     *
     * @param string|null $str
     * @return string
     */
    public static function geoToLat(?string $str = null): string
    {
        if ($str) {
            $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
            $result = array_map(static function (string $char) {
                return self::$geoLatMapper[$char] ?? $char;
            }, $chars);

            return implode('', $result);
        }

        return '';
    }
}
