<div class="relative flex items-center"
     x-data="{
         code: 'usd',
         dropdown: false,
         maxBuyPrice: 0,
         minSellPrice: 0,
         items: [],
         filteredItems:[],
         filterDate(){
            filteredItems = this.items.filter(item => item.code.toLowerCase() === this.code)
            this.filteredItems = filteredItems;
            let minSellPrice = null;
            let maxBuyPrice = null;
            for(item in filteredItems)
            {
                maxBuyPrice = (filteredItems[item].buyRate > maxBuyPrice || maxBuyPrice === null) ? filteredItems[item].buyRate : maxBuyPrice;
                minSellPrice = (filteredItems[item].sellRate < minSellPrice || minSellPrice === null) ? filteredItems[item].sellRate : minSellPrice;
            }
            this.maxBuyPrice = maxBuyPrice;
            this.minSellPrice = minSellPrice;
         }
         }"
     x-init="items = JSON.parse('{{$currencies}}'); filterDate();"
>
    <div class=" ml-2">
        <div class="cursor-pointer flex items-center" x-on:click="dropdown = !dropdown">
            <div class="w-7 relative pointer-events-none" x-show="code === 'usd'">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
            <div class="w-7 relative pointer-events-none" x-show="code === 'eur'">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
            <div class="w-7 relative pointer-events-none" x-show="code === 'gbp'">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="#333" class="ml-2 w-4 h-3 bi bi-caret-down-fill" viewBox="0 0 16 16">
                <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
            </svg>
        </div>
        <div class="absolute flex flex-col z-10 bg-white shadow p-1 gap-1 rounded-md" x-show="dropdown" x-on:click.outside="dropdown = false">
            <div class="w-7 relative cursor-pointer" x-on:click="code = 'usd'; dropdown = false; filterDate()">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/USD.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
            <div class="w-7 relative cursor-pointer" x-on:click="code = 'eur'; dropdown = false; filterDate()">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/EUR.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
            <div class="w-7 relative cursor-pointer" x-on:click="code = 'gbp'; dropdown = false; filterDate()">
                <img src="{{ Vite::asset('resources/imgs/currency/flags/GBP.png') }}" class="rounded-lg h-full w-full object-cover">
            </div>
        </div>
    </div>

    <div class="carousel flex items-center cursor-pointer">
        <div class="group flex items-center justify-center font-normal text-gray-800 w-full gap-4">

            <template x-for="item in filteredItems" :key="item.id">
                <div class="card flex items-center gap-2">

                    <div class="grid text-xx w-9">
                        <img :src="item.providerLogo">
                    </div>
                    <div class="grid text-xx">
                        <small class="text-gray-700" :class="(maxBuyPrice === item.buyRate) ? 'text-red-400 font-medium' : ''">ყიდვა</small>
                        <span x-text="item.buyRate" class="text-sm"></span>
                    </div>
                    <div class="grid text-xx">
                        <small class="text-gray-700" :class="(minSellPrice === item.sellRate) ? 'text-red-400 font-medium' : ''">გაყიდვა</small>
                        <span x-text="item.sellRate" class="text-sm"></span>
                    </div>
                </div>
            </template>
        </div>

        <div class="group flex items-center justify-center font-normal text-gray-800 w-full gap-4">

            <template x-for="item in filteredItems" :key="item.id + '2'">
                <div class="card flex items-center gap-2">

                    <div class="grid text-xx w-9">
                        <img :src="item.providerLogo">
                    </div>
                    <div class="grid text-xx">
                        <small class="text-gray-700" :class="(maxBuyPrice === item.buyRate) ? 'text-red-400 font-medium' : ''">ყიდვა</small>
                        <span x-text="item.buyRate" class="text-sm"></span>
                    </div>
                    <div class="grid text-xx">
                        <small class="text-gray-700" :class="(minSellPrice === item.sellRate) ? 'text-red-400 font-medium' : ''">გაყიდვა</small>
                        <span x-text="item.sellRate" class="text-sm"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
