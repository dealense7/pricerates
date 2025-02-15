<?php

declare(strict_types=1);

namespace App\Enums\Store;

enum Store: int
{
    case Goodwill  = 1;
    case Carrefour = 2;
    case Europroduct      = 3;
    case Magniti   = 4;
    case Fresco     = 5;
    case Nikora     = 6;
}
