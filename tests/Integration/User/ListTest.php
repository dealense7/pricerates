<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('users'));

    $response->assertUnauthorized();
});

it('should raise forbidden on return items list for permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('users'));

    $response->assertForbidden();
});

it('should raise forbidden if can not see everyone and has no company', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);
    $response = $this->jsonWithHeader('GET', $this->url('users'));

    $response->assertForbidden();
});

it('it should return list of only company colleagues', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();

    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId(),],
        ['permissions' => $this->getPermissions(['read'])],
    );
    // Same company guy
    ProvidesTestingData::createRandomUsers(['company_id' => $company->getId()]);
    // Different company guy
    ProvidesTestingData::createRandomUsers();

    $response = $this->jsonWithHeader('GET', $this->url('users'));

    $response->assertJsonDataCount(2);

    $response->assertJsonDataCollectionStructure($this->getUserStructure([
        'company',
    ]));
});

it('it should return list of everyone', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();

    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId(),],
        ['permissions' => $this->getPermissions(['read', 'read_everyone'])],
    );
    // Same company guy
    ProvidesTestingData::createRandomUsers(['company_id' => $company->getId()]);
    // Different company guy
    ProvidesTestingData::createRandomUsers();

    $response = $this->jsonWithHeader('GET', $this->url('users'));

    $response->assertJsonDataCount(3);

    $response->assertJsonDataCollectionStructure($this->getUserStructure());
});

it('should return items list', function (array $data) {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read', 'read_everyone'])],
    );

    $data['dataCallback']();

    $response = $this->jsonWithHeader('GET', $this->url('users'), Arr::get($data, 'request', []));

    $response->assertOk();
    $response->assertJsonDataPagination(Arr::get($data, 'response'));
    $response->assertJsonDataCollectionStructure($this->getUserStructure());
})->with('dataForListing');

dataset('dataForListing', static function () {
    $return                  = [];
    $return['filters-empty'] = [
        'data' => [
            'dataCallback' => static function () {
                ProvidesTestingData::createRandomUsers([], [], 3);
            },
            'request'      => [
                'filters' => [],
            ],
            'response'     => [
                'page'    => 1,
                'perPage' => 25,
                'count'   => 4,
                'total'   => 4,
            ],
        ],
    ];

    $name                      = 'userName';
    $return['filters-keyword'] = [
        'data' => [
            'dataCallback' => static function () use ($name) {
                ProvidesTestingData::createRandomUsers([
                    'username' => $name,
                ], [], 1);

                ProvidesTestingData::createRandomUsers();
            },
            'request'      => [
                'filters' => [
                    'username' => $name,
                ],
            ],
            'response'     => [
                'page'    => 1,
                'perPage' => 25,
                'count'   => 1,
                'total'   => 1,
            ],
        ],
    ];

    $return['use-pagination'] = [
        'data' => [
            'dataCallback' => static function () {
                ProvidesTestingData::createRandomUsers([], [], 5);
            },
            'request'      => [
                'page'    => 2,
                'perPage' => 2,
            ],
            'response'     => [
                'page'    => 2,
                'perPage' => 2,
                'count'   => 2,
                'total'   => 6,
            ],
        ],
    ];

    return $return;
});

it('should raise validation error on list for max per page data', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read', 'read_everyone'])],
    );

    $requestData = [
        'page'    => 1,
        'perPage' => 1000001,
    ];

    $response = $this->jsonWithHeader('GET', $this->url('users'), $requestData);

    $response->assertInvalidData();
});
