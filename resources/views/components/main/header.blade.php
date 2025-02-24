<div>
    <div class="border-b">
        <div class="w-full sm:w-8/12 mx-auto border-r border-l">
            <div class="w-3/4 mx-auto border-r border-l border-dashed">
                <div class="py-24 px-5  grid">
                    <h2 class="font-bold text-2xl text-neutral-900">
                        პროლოგი
                    </h2>
                    <p class="font-normal text-xs text-neutral-800 mt-1">
                        დაგეხმარებით იპოვოთ პროდუქტი უკეთეს ფასად და დაზოგოთ რაც შეიძლება მეტი.
                    </p>
                    <p class="font-normal text-sm text-neutral-500 mt-1">
                        <small>ფასები გადმოტანილია სხვა-და-სხვა პროვაიდერებიდან, შეიძლება არ იყოს რეალური.</small>
                    </p>
                    <div class="flex gap-3 text-xx mt-1.5 font-medium">
                        <button class="bg-gray-700 hover:bg-gray-800 transition-all duration-150 rounded-md text-white p-2 px-3">დაგვიკავშირდი</button>
                        <button class="text-gray-700 hover:text-gray-800 transition-all duration-150 rounded-md bg-white border-gray-700 border hover:border-gray-800 p-2 px-3">გამოიწერერ სიახლეები</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-b">
        <div class="w-full sm:w-8/12 mx-auto border-r border-l">
            <x-main.widget.currency :currencies="$currencies"/>
        </div>
    </div>
</div>
