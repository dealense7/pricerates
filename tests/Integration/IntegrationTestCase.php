<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Support\Testing\ProvidesItemStructures;
use App\Support\Testing\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Tests\TestCase as BaseAppTestCase;

abstract class IntegrationTestCase extends BaseAppTestCase
{
    use ProvidesItemStructures;
    use MakesHttpRequests {
        json as baseJson;
    }

    protected function initialize(Application $app): void
    {
        parent::initialize($app);

        Response::setSuccessResponseStructure($this->getSuccessStructure());
        Response::setErrorResponseStructure($this->getErrorStructure());

//        $this->clearCache();
    }

    public function jsonWithHeader(
        string $method,
        string $uri,
        array $data = [],
        array $headers = [],
    ): Response {
        return $this->json($method, $uri, $data, $headers);
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0, string $version = 'v1')
    {
        $headers = [
            'X-Api-Version' => $version,
            ...$headers,
        ];

        $response = $this->baseJson($method, $uri, $data, $headers);

        // You can create your CustomResponse class and convert the TestResponse to CustomResponse here
        return new Response($response->baseResponse);
    }

    protected function createTestResponse($response, $request): Response
    {
        return Response::fromBaseResponse($response, $request);
    }

    protected function url(string $url): string
    {
        return 'api/' . $url;
    }

    protected function clearCache(): IntegrationTestCase
    {
        app('cache.store')->flush();

        return $this;
    }
}
