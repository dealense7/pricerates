<div>
    <div :key="{{$item['id']}}"
         x-data="{
             itemPrices: [],
             itemMinPrice:'{{$item['prices'][0]['price']}}',
             itemMaxPrice: '{{end($item['prices'])['price']}}',
             itemBrandName: `{{$item['brandName']}}`,
             itemDisplayName: `{{$item['name']}}`
         }"
         x-init="itemPrices = JSON.parse(`{{json_encode($item['prices'])}}`)"
         class="col-span-1 p-2 {{$index === 4 ? 'hidden md:block' : ''}} {{$index === 6 ? 'hidden xl:block' : ''}} {{$index === 5 ? 'hidden md:block lg:hidden xl:block' : ''}}"
         x-on:click="
            setPrices(itemPrices, itemMaxPrice);
            imgUrl = '{{$item['image']}}';
            title = itemDisplayName;
            brandName = itemBrandName;
            open = true
         "
    >
        <div class="w-full h-48 relative bg-white shadow-sm rounded-sm p-5">
            <img src="{{ $item['image']}}" class="object-contain w-full h-full"/>
            <span class="absolute right-1 bottom-1 text-xs">
                <small>{{ $item['prices'][0]['createdAt'] }}</small>
            </span>


                        <span class="absolute left-1 font-medium bottom-1 text-sm {{ $item['unit']['class'] }} px-0.5 text-white">
                            <small>{{ $item['unitAmount'] }} <small>{{ $item['unit']['label'] }}</small></small>
                        </span>
        </div>

        <h3 class="my-1 text-xs font-bold text-gray-800" x-text="itemBrandName"></h3>
        <span class="text-xx mt-1 grid font-normal text-neutral-700 h-8">
            <span class="line-clamp-2" x-text="itemDisplayName"></span>
        </span>

        <div class="flex items-center justify-between">
            <h3 class="my-1 text-xs font-bold text-gray-950">
                ₾ <span x-text="itemMinPrice"></span>
            </h3>
            <div class="flex items-center my-1 text-xx font-normal text-gray-950">
                <svg xmlns="http://www.w3.org/2000/svg" transform="rotate(180)" width="16" height="16"
                     fill="#00A86B" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                </svg>
                ₾ <span x-text="(itemMaxPrice - itemMinPrice).toFixed(2)"></span>
            </div>
        </div>
    </div>
</div>
