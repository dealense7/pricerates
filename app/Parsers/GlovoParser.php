<?php

declare(strict_types=1);

namespace App\Parsers;

use App\Enums\General\Category;
use App\Enums\General\Provider;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class GlovoParser extends Parser
{
    public function parse(...$args): void
    {
        $this->setStoreId($args['storeId']);
        foreach ($args['categories'] as $categoryKey => $category) {
            // Fetch urls
            $urls = self::getApiUrls($args['url'], $category);
            $urls = array_unique($urls);

            // Fetch products from API
            foreach ($urls as $url) {
                $items = self::fetchItems($args['apiBase'], $url);

                $items = $this->formatItems($items, Category::from($categoryKey));

                $this->storeItems($items);
            }
        }
    }

    protected function getProviderId(): int
    {
        return Provider::Glovo->value;
    }

    protected function setStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    protected function getName(array $item): string
    {
        return Arr::get($item, 'data.name');
    }

    protected function getPrice(array $item): int
    {
        return ((float) Arr::get($item, 'data.price')) * 100;
    }

    protected function getBarCode(array $item): string
    {
        return (int) filter_var(Arr::get($item, 'data.externalId'), FILTER_SANITIZE_NUMBER_INT);
    }

    protected function getBarCodeFromName(array $item): string
    {
        $array = explode('/', Arr::get($item, 'data.name', ''));

        return (int) filter_var(end($array), FILTER_SANITIZE_NUMBER_INT);
    }

    protected function getBarCodeFromImage(array $item): ?string
    {

        $url = Arr::get($item, 'data.imageUrl', '');

        if (preg_match('/_(\d+)\.jpg$/', $url, $matches)) {
            $barcode = $matches[1];
            echo $barcode; // Output: 4860103352088
        }

        return null;
    }

    protected function getImageUrl(array $item): ?string
    {
        return Arr::get($item, 'data.imageUrl');
    }

    private function getApiUrls(string $url, array $categories): array
    {
        $categoriesJson = json_encode(array_values($categories)); // Ensure it's indexed array

        $data = Browsershot::url($url)
            ->waitUntilNetworkIdle()
            ->evaluate("
                (() => {
                    const commonExcludedCategories = $categoriesJson; // Inject PHP array into JS

                    const extractUrls = (nuxtData) => {
                        const urls = [];

                        const traverse = (obj) => {
                            if (obj && typeof obj === 'object') {
                                if (
                                    obj.name &&
                                    obj.slug &&
                                    commonExcludedCategories.some(category => obj.name.includes(category)) &&
                                    obj.action.data.path.includes('-sc.')
                                ) {
                                    urls.push(obj.action.data.path);
                                }
                                Object.values(obj).forEach(traverse);
                            }
                        };

                        if (typeof window.__NUXT__ !== 'undefined') {
                            traverse(window.__NUXT__);
                        }

                        return urls;
                    };

                    return JSON.stringify(extractUrls(window.__NUXT__ || {}));
                })();
            ");

        return json_decode($data, true);
    }

    private function fetchItems(string $api, string $node): array
    {
        $faker    = Faker::create();
        $response = Http::withHeaders([
            'accept'                      => 'application/json',
            'glovo-api-version'           => '14',
            'glovo-app-platform'          => 'web',
            'glovo-app-type'              => 'customer',
            'glovo-app-version'           => '7',
            'glovo-language-code'         => 'en',
            'glovo-location-city-code'    => 'TBI',
            'glovo-location-country-code' => 'GE',
            'user-agent'                  => $faker->userAgent, // Generate a fake user-agent
            'glovo-request-id'            => Str::uuid()->toString(),
            'glovo-dynamic-session-id'    => Str::uuid()->toString(),
        ])->get($api . $node, ['nodeType' => 'DEEP_LINK', 'link' => $node,]);

        $responseJson = $response->json();

        $items = [];
        $fetchedItems = Arr::get($responseJson, 'data.body', []);

        foreach ($fetchedItems as $item) {
            $items = [
                ...$items,
                ...Arr::get($item, 'data.elements', []),
            ];
        }

        return $items;
    }
}
