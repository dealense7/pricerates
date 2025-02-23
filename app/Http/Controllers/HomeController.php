<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\Currency\CurrencyServiceContract;
use App\Contracts\Services\Gas\GasServiceContract;
use App\Models\Currency\Currency;
use App\Services\V1\Products\ProductService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(
        CurrencyServiceContract $currencyService,
        GasServiceContract $gasService,
        ProductService $productService,
    ): View
    {
        $currencies   = $currencyService->getItems();
        $gasItems     = $gasService->getItems();
        $popularItems = $productService->getMostPopularItems()->random(7);
        $randomCategoryItems = $productService->getRandomCategoryItems();

        return view('welcome', compact('currencies', 'popularItems', 'gasItems', 'randomCategoryItems'));
    }
}
