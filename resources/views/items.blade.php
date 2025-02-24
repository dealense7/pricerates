<x-layout>
    <div class="border-b">
        <div class="w-full sm:w-8/12 mx-auto border-r border-l">
            <x-main.widget.currency :currencies="$currencies"/>
        </div>
    </div>


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
            <div class="border-b">
                <div
                    class="w-full mx-auto font-normal grid lg:grid-cols-5 xl:grid-cols-7 grid-cols-2 md:grid-cols-3  gap-1">

                    @foreach($items as $index => $item)
                        <x-product.item :item="$item"/>
                    @endforeach
                </div>
            </div>
            <div class="w-full">
                {{$items->links()}}
            </div>
        </div>


        {{--  Modal --}}
        <x-product.modal/>

    </div>



</x-layout>
