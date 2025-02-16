<!-- yeah, I will forget what is happening in this component soon, so I dont care -->
<!-- update: 16-02-2025 I forgot -->

<div class="grid grid-cols-10 overflow-hidden" x-data="{
    show: 'USD',
    open: false,
    dropdown: false,
    modalDropdown: false,
    currencies: {{json_encode($currencies->toArray(), true)}},
    calculateValue: 100.00,
    calculatedBuyMax: null,
    calculatedBuyMin: null,
    calculatedSellMin: null,
    calculatedSellMax: null,
    changeCurrency(code) {
        this.show = code;
        this.calculatedBuyMax = null;
        this.calculatedBuyMin = null;
        this.calculatedSellMin = null;
        this.calculatedSellMax = null;
    },
    maxBuyRate(currencyCode) {
        if(this.calculatedBuyMax !== null){
            return this.calculatedBuyMax;
        }

        this.calculatedBuyMax = Math.max(...this.currencies.filter(c => c.currency.code === currencyCode).map(c => c.buy_rate));

        return this.calculatedBuyMax;
    },
    minBuyRate(currencyCode) {
        if(this.calculatedBuyMin !== null){
            return this.calculatedBuyMin;
        }

        this.calculatedBuyMin = Math.min(...this.currencies.filter(c => c.currency.code === currencyCode).map(c => c.buy_rate));

        return this.calculatedBuyMin;
    },
    minSellRate(currencyCode) {
        if(this.calculatedSellMin !== null){
            return this.calculatedSellMin;
        }

        this.calculatedSellMin = Math.min(...this.currencies.filter(c => c.currency.code === currencyCode).map(c => c.sell_rate));

        return this.calculatedSellMin;
    },
    maxSellRate(currencyCode) {
        if(this.calculatedSellMax !== null){
            return this.calculatedSellMax;
        }

        this.calculatedSellMax = Math.max(...this.currencies.filter(c => c.currency.code === currencyCode).map(c => c.sell_rate));

        return this.calculatedSellMax;
    },
}">
    <div class="col-span-2 sm:col-span-1 flex items-center justify-center">
        <div>
            <div class="cursor-pointer flex items-center" x-on:click="dropdown = !dropdown">
                <div class="w-7 relative pointer-events-none" x-show="show === 'USD'">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
                <div class="w-7 relative pointer-events-none" x-show="show === 'EUR'">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
                <div class="w-7 relative pointer-events-none" x-show="show === 'GBP'">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="#333" class="ml-2 w-4 h-3 bi bi-caret-down-fill" viewBox="0 0 16 16">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                </svg>
            </div>
            <div class="absolute flex flex-col  bg-white shadow p-1 gap-1 rounded-md" x-show="dropdown" x-on:click.outside="dropdown = false">
                <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('USD')">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
                <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('EUR')">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
                <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('GBP')">
                    <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
                </div>
            </div>
        </div>
    </div>
    <div class="slider cursor-pointer overflow-hidden w-full col-span-8" x-on:click="open = true">
        <div class="slide-track">
            @foreach($currencies as $currency)
                <div class="slide items-center justify-center gap-2 col-span-1 leading-none flex" x-show="show === '{{$currency->currency->code}}'">
                    <div class="w-11 relative">
                        <!-- Provider Logo -->
                        <img src="{{ Vite::asset('resources/imgs/'.$currency->provider->logo_url) }}" class="h-full w-full object-contain">
                    </div>

                    <div class="grid grid-cols-2">
                        <div class="ml-2 col-span-1 font-normal flex items-center">
                            <span class="text-base">{{number_format($currency->buy_rate, 3)}}</span>
                            <div class="grid" x-show="{{$currency->buy_rate}} >= maxBuyRate(show)">
                                <div class="flex items-center text-xs -top-1 relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#00A86B" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                                    </svg>
                                    <small class="font-medium">TOP</small>
                                </div>
                                <div class="flex items-center text-xs pl-2 -top-1.5 relative">
                                    <small x-text="(maxBuyRate(show) - minBuyRate(show)).toFixed(3)"></small>
                                </div>
                            </div>
                            <div class="flex items-center text-xs -top-1 relative" x-show="{{$currency->buy_rate}} < maxBuyRate(show)">
                                <!-- Show red arrow and difference from max buy rate -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff2c2c" class="bi bi-arrow-down-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/>
                                </svg>
                                <small x-text="(maxBuyRate(show) - {{$currency->buy_rate}}).toFixed(3)"></small>
                            </div>
                        </div>

                        <div class="ml-2 font-normal col-span-1 text-base flex items-center">
                            <span class="text-base">{{number_format($currency->sell_rate, 3)}}</span>
                            <div class="grid" x-show="{{$currency->sell_rate}} <= minSellRate(show)">
                                <div class="flex items-center text-xs -top-1 relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#00A86B" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                                    </svg>
                                    <small class="font-medium">TOP</small>
                                </div>
                                <div class="flex items-center text-xs pl-2 -top-1.5 relative">
                                    <small x-text="(maxSellRate(show) - minSellRate(show)).toFixed(3)"></small>
                                </div>
                            </div>
                            <div class="flex items-center text-xs -top-1 relative" x-show="{{$currency->sell_rate}} > minSellRate(show)">
                                <!-- Show red arrow and difference from max buy rate -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff2c2c" class="bi bi-arrow-down-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/>
                                </svg>
                                <small x-text="({{$currency->sell_rate}} - minSellRate(show)).toFixed(3)"></small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div @keydown.window.escape="open = false" x-cloak x-show="open" class="relative z-10" aria-labelledby="modal-title" x-ref="dialog" aria-modal="true">

        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-description="Background backdrop, show/hide based on modal state." class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>


        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-description="Modal panel, show/hide based on modal state." class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.away="open = false">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start flex-col gap-3">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-sm font-bold text-gray-800" id="modal-title">ვალუტის კალკულატორი</h3>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500 font-normal">მეტი ინფორმაციისთვის ეწვიეთ მითითებული კომპანიის ვებსაიტს. სხვა-და-სხვა ოპერაციების დროს კურსი შეიძლება განსხვავებული იყოს.</p>
                                </div>
                            </div>
                            <div class="mt-3 w-full grid grid-cols-3 sm:grid-cols-7">
                                <div class="flex items-center justify-between gap-2 col-span-3 w-full bg-gray-100 p-1 rounded-sm">
                                    <input x-model="calculateValue" class="border-0 w-24 font-normal p-0 margin-0 bg-transparent text-gray-800 text-sm" type="number" step="0.01" min="1" max="10000">
                                    <span class="text-xs font-normal border-l pl-2 text-gray-500">USD</span>
                                    <div class="relative cursor-pointer">
                                        <div class="cursor-pointer flex items-center" x-on:click="modalDropdown = !modalDropdown">
                                            <div class="w-7 relative pointer-events-none" x-show="show === 'USD'">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                            <div class="w-7 relative pointer-events-none" x-show="show === 'EUR'">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                            <div class="w-7 relative pointer-events-none" x-show="show === 'GBP'">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="#333" class="ml-2 w-4 h-3 bi bi-caret-down-fill" viewBox="0 0 16 16">
                                                <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                                            </svg>
                                        </div>
                                        <div class="absolute flex flex-col  bg-white shadow p-1 gap-1 rounded-md" x-show="modalDropdown" x-on:click.outside="modalDropdown = false">
                                            <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('USD')">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                            <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('EUR')">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                            <div class="w-7 relative cursor-pointer" x-on:click="changeCurrency('GBP')">
                                                <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 col-span-1 justify-center cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#2962FF" class="w-5 h-5 bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                    </svg>
                                </div>
                                <div class="flex items-center justify-between gap-2 col-span-3 w-full bg-gray-100 p-1 rounded-sm">
                                    <input disabled x-model="(maxBuyRate(show) * calculateValue).toFixed(2)" class="border-0 w-24 font-normal p-0 margin-0 bg-transparent text-gray-600 text-sm" step="0.01">
                                    <span class="text-xs font-normal border-l pl-2 text-gray-500">GEL</span>
                                    <div class="w-7 relative cursor-pointer">
                                        <img src="{{ Vite::asset('resources/imgs/currency/flags/GEL.png') }}" class="rounded-lg h-full w-full object-cover">
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($currencies as $currency)
                                    <div class="slide items-center justify-center gap-2 col-span-1 leading-none flex" x-show="show === '{{$currency->currency->code}}'">
                                        <div class="w-11 relative">
                                            <!-- Provider Logo -->
                                            <img src="{{ Vite::asset('resources/imgs/'.$currency->provider->logo_url) }}" class="h-full w-full object-contain">
                                        </div>

                                        <div class="grid grid-cols-2">
                                            <div class="ml-2 col-span-1 font-normal flex items-center">
                                                <span class="text-base">{{number_format($currency->buy_rate, 3)}}</span>
                                                <div class="grid" x-show="{{$currency->buy_rate}} >= maxBuyRate(show)">
                                                    <div class="flex items-center text-xs -top-1 relative">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#00A86B" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                                                        </svg>
                                                        <small class="font-medium">TOP</small>
                                                    </div>
                                                    <div class="flex items-center text-xs pl-2 -top-1.5 relative">
                                                        <small x-text="(maxBuyRate(show) - minBuyRate(show)).toFixed(3)"></small>
                                                    </div>
                                                </div>
                                                <div class="flex items-center text-xs -top-1 relative" x-show="{{$currency->buy_rate}} < maxBuyRate(show)">
                                                    <!-- Show red arrow and difference from max buy rate -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff2c2c" class="bi bi-arrow-down-short" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/>
                                                    </svg>
                                                    <small x-text="(maxBuyRate(show) - {{$currency->buy_rate}}).toFixed(3)"></small>
                                                </div>
                                            </div>

                                            <div class="ml-2 font-normal col-span-1 text-base flex items-center">
                                                <span class="text-base">{{number_format($currency->sell_rate, 3)}}</span>
                                                <div class="grid" x-show="{{$currency->sell_rate}} <= minSellRate(show)">
                                                    <div class="flex items-center text-xs -top-1 relative">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#00A86B" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                                                        </svg>
                                                        <small class="font-medium">TOP</small>
                                                    </div>
                                                    <div class="flex items-center text-xs pl-2 -top-1.5 relative">
                                                        <small x-text="(maxSellRate(show) - minSellRate(show)).toFixed(3)"></small>
                                                    </div>
                                                </div>
                                                <div class="flex items-center text-xs -top-1 relative" x-show="{{$currency->sell_rate}} > minSellRate(show)">
                                                    <!-- Show red arrow and difference from max buy rate -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff2c2c" class="bi bi-arrow-down-short" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/>
                                                    </svg>
                                                    <small x-text="({{$currency->sell_rate}} - minSellRate(show)).toFixed(3)"></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-xs font-normal text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="open = false">დახურვა</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

