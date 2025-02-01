<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('acl/roles'));

    $response->assertUnauthorized();
});

it('should raise forbidden on return items list for permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('acl/roles'));

    $response->assertForbidden();
});

it('should return items list', function (array $data) {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read'])],
    );

    $data['dataCallback']();

    $response = $this->jsonWithHeader('GET', $this->url('acl/roles'), Arr::get($data, 'request', []));

    $response->assertOk();
    $response->assertJsonDataPagination(Arr::get($data, 'response'));
    $response->assertJsonDataCollectionStructure($this->getRoleStructure());
})->with('dataForListing');

dataset('dataForListing', static function () {
    $return                  = [];
    $return['filters-empty'] = [
        'data' => [
            'dataCallback' => static function () {
                ProvidesTestingData::createRoleRandomItem([], 4);
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

    $return['use-pagination'] = [
        'data' => [
            'dataCallback' => static function () {
                ProvidesTestingData::createRoleRandomItem([], 5);
            },
            'request'      => [
                'page'    => 2,
                'perPage' => 2,
            ],
            'response'     => [
                'page'    => 2,
                'perPage' => 2,
                'count'   => 2,
                'total'   => 5,
            ],
        ],
    ];

    return $return;
});
