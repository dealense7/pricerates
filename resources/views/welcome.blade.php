<x-layout>
    <x-main.header :currencies="$currencies"/>

    <div class="border-b"
         x-data="{
          prices: [],
          maxPrice: 0,
          open: false,
          imgUrl: null,
          brandName: 'Brand Name',
          title: 'Title',
          setPrices(items, maxPrice){
            this.prices = items;
            this.maxPrice = maxPrice;
          }
        }"
    >
        <div class="w-full sm:w-8/12 mx-auto font-normal border-r border-l grid lg:grid-cols-5 xl:grid-cols-7 grid-cols-2 md:grid-cols-3  gap-1">
            @foreach($popularItems as $index => $item)
                <x-product.item :item="$item" :index="$index"/>
            @endforeach
        </div>

        {{--  Modal --}}
        <x-product.modal/>
    </div>


    <x-main.widget.gas :items="$gasItems"/>

    <div class="border-b"
         x-data="{
          prices: [],
          maxPrice: 0,
          open: false,
          imgUrl: null,
          brandName: 'Brand Name',
          title: 'Title',
          setPrices(items, maxPrice){
            this.prices = items;
            this.maxPrice = maxPrice;
          }
        }"
    >
        <div class="w-full sm:w-8/12 mx-auto font-normal border-r border-l flex flex-col">
            @foreach($randomCategoryItems as $category)
                <div class="border-b">
                    <div class="flex items-center justify-between">
                        <div class="grid p-3">
                            <h2 class="text-sm font-medium">{{$category->name}}</h2>
                        </div>
                        <a href="/" class="grid p-3">
                            <span class="text-xx">ყველას ნახვა</span>
                            <span class="text-xx mt-0.5 font-normal text-gray-600"><small>სულ: {{$category->products_count}}</small></span>
                        </a>
                    </div>

                    <div class="w-full mx-auto font-normal grid lg:grid-cols-5 xl:grid-cols-7 grid-cols-2 md:grid-cols-3  gap-1">
                        @foreach($category->products as $index => $item)
                            <x-product.item :item="$item" :index="$index"/>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>


        {{--  Modal --}}
        <x-product.modal/>
    </div>


</x-layout>
