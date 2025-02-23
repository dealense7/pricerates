<?php

declare(strict_types=1);

namespace App\View\Components\Main;

use App\Support\Collection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(
        public Collection $currencies,
    ) {
        //
    }

    public function render(): View | Closure | string
    {
        return view('components.main.header');
    }
}
