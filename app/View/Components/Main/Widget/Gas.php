<?php

namespace App\View\Components\Main\Widget;

use App\Support\Collection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Gas extends Component
{
    public function __construct(public Collection $items)
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.main.widget.gas');
    }
}
