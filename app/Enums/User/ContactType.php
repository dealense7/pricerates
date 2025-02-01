<?php

declare(strict_types=1);

namespace App\Enums\User;

use App\Enums\EnumTrait;

enum ContactType: int
{
    use EnumTrait;

    case EMAIL = 1;
    case PHONE = 2;

    public function getText(): string
    {
        return match ($this) {
            self::EMAIL => __('user/contact-types.' . strtolower(self::EMAIL->name)),
            self::PHONE => __('user/contact-types.' . strtolower(self::PHONE->name)),
        };
    }
}
