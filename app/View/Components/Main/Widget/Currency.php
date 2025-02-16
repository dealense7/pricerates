<?php

namespace App\View\Components\Main\Widget;

use App\Support\Collection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Currency extends Component
{
    public function __construct(
        public Collection $currencies,
    )
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.main.widget.currency');
    }
}
