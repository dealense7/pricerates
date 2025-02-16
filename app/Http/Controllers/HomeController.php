<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\Currency\CurrencyServiceContract;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(
        CurrencyServiceContract $currencyService
    ): View
    {
        $currencies = $currencyService->getItems();

        return view('welcome', compact('currencies'));
    }
}
