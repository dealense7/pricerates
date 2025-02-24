<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\Currency\CurrencyServiceContract;
use App\Contracts\Services\Gas\GasServiceContract;
use App\Http\Requests\ItemsPageIndex;
use App\Services\V1\Products\ProductService;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(
        CurrencyServiceContract $currencyService,
        GasServiceContract $gasService,
        ProductService $productService,
    ): View
    {
        $currencies          = $currencyService->getItems();
        $gasItems            = $gasService->getItems();
        $popularItems        = $productService->getMostPopularItems()->random(7);
        $randomCategoryItems = $productService->getRandomCategoryItems();

        return view('welcome', compact('currencies', 'popularItems', 'gasItems', 'randomCategoryItems'));
    }

    public function items(
        ItemsPageIndex $request,
        CurrencyServiceContract $currencyService,
        ProductService $productService,
    ): View
    {
        $validated = $request->validated();

        $currencies = $currencyService->getItems();
        $items      = $productService->getItems(Arr::get($validated, 'filters', []));

        return view('items', compact('currencies', 'items'));
    }
}
