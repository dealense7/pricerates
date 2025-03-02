<div class="relative z-10 ease-out duration-300" x-cloak x-show="open" @keyup.escape.window="open = false"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

            <div
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                x-on:click.away="open = false">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="grid grid-cols-3">
                        <div class="w-full col-span-1 h-32 relative" x-show="imgUrl !== null">
                            <img :src="imgUrl" class="object-contain w-full h-full">
                        </div>
                        <div class="mt-3 col-span-2 text-left sm:mt-0 sm:ml-4 sm:text-left">
                            <h4 class="text-xx font-bold uppercase text-gray-800" x-text="brandName">N/A</h4>
                            <h3 class="text-xs font-normal text-gray-700" x-text="title">N/A</h3>
                            <div class="mt-2">
                                <p class="text-normal text-xx text-gray-500">პროდუქტის ფასები გადმოტანილია
                                    სხვა-და-სხვა ონლაინ პროვაიდერებიდან, ფასი შეიძლება არ იყოს რეალური.</p>
                            </div>
                            <template x-for="price in prices" :key="price.providerName">
                                <div class="my-2 border-b flex items-center justify-between">
                                    <div class="grid">
                                        <h4 class="font-medium text-xs text-gray-700"
                                            x-text="price.providerName"></h4>
                                        <span class="text-xx font-normal text-gray-600">
                                                    <small x-text="price.createdAt"></small>
                                                </span>
                                    </div>
                                    <div class="grid">
                                                <span class="text-sm font-medium text-gray-900">₾ <span
                                                        x-text="price.price"></span></span>
                                        <div class="flex items-center -ml-1"
                                             x-show="(maxPrice - price.price) > 0">
                                            <svg xmlns="http://www.w3.org/2000/svg" transform="rotate(180)"
                                                 width="16" height="16" fill="#00A86B"
                                                 class="bi bi-arrow-up-short" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
                                            </svg>
                                            <span class="text-xs font-normal text-gray-600">
                                                        <small x-text="(maxPrice - price.price).toFixed(2)"></small>
                                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button x-on:click="open = false" type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-xs font-normal text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        დახურვა
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
