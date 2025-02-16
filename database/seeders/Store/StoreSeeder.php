<?php

declare(strict_types=1);

namespace Database\Seeders\Store;

use App\Enums\General\Category;
use App\Enums\General\Provider;
use App\Enums\Store\Store;
use App\Models\Store\Store as StoreModel;
use Illuminate\Cache\TaggableStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $commonExcludedCategories = [
            Category::Garden->value     => [
                'ბოსტნეული', 'ხილი', 'მწვანილი',
            ],
            Category::Dairy->value      => [
                'კვერცხი', 'ყველი', 'არაჟანი', 'მაწონი', 'რძე', 'კარაქი', 'მარგარინი', 'იოგურტი',
            ],
            Category::Grocery->value    => [
                'ზეთი', 'კონსერვაცია', 'სოუსები', 'მაკარონი', 'შაქარი', 'მარილი', 'სანელებ', 'მარცვლეული', 'ფქვილი', 'ბურღული', 'პაშტეტი', 'ნახევარფაბრიკატ',
            ],
            Category::NonAlcohol->value => [
                'წყალი', 'გაზიანი სასმელები', 'წვენი', 'ჩაი', 'ყავა', 'ენერგეტიკული',
            ],
            Category::Alcohol->value    => [
                'ტონიკი', 'ლუდი', 'ღვინო', 'არაყი', 'ჭაჭა', 'ვისკი', 'კონიაკი', 'ლიქიორი', 'რომი', 'ტეკილა', 'კოქტეილი',
            ],
            Category::Bread->value      => [
                'პური', 'ლავაში',
            ],
            Category::Meat->value       => [
                'ხორცი', 'ქათამი', 'თევზი', 'ზღვის', 'სოსისი', 'სარდელი', 'ძეხვ',
            ],
        ];

        $items = [
            [
                'id'   => Store::Goodwill->value,
                'name' => 'Goodwill',
                'slug' => 'goodwill',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/goodwill-1-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
            [
                'id'   => Store::Carrefour->value,
                'name' => 'Carrefour',
                'slug' => 'carrefour',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/1carrefour-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
            [
                'id'   => Store::Europroduct->value,
                'name' => 'EuroProduct',
                'slug' => 'europroduct',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/europroduct-c-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
            [
                'id'   => Store::Magniti->value,
                'name' => 'Magniti',
                'slug' => 'magniti',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/magniti-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
            [
                'id'   => Store::Fresco->value,
                'name' => 'Fresco',
                'slug' => 'fresco',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/fresco-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
            [
                'id'   => Store::Nikora->value,
                'name' => 'Nikora',
                'slug' => 'nikora',
                'show' => true,
                'urls' => [
                    [
                        'provider_id' => Provider::Glovo->value,
                        'meta'        => [
                            'url'        => 'https://glovoapp.com/ge/en/tbilisi/nikora-test-tbi/',
                            'api_base'   => 'https://api.glovoapp.com/v3/',
                            'categories' => $commonExcludedCategories,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($items as $item) {
            $store = (new StoreModel())->create(Arr::only($item, ['id', 'name', 'show', 'slug']));
            $store->urls()->createMany($item['urls']);
        }

        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags([StoreModel::class])->flush();
        }
    }
}
