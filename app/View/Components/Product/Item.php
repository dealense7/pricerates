<?php

namespace App\View\Components\Product;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    public function __construct(public array $item, public ?int $index = null)
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.product.item');
    }
}
