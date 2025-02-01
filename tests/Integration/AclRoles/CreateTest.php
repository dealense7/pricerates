<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('POST', $this->url('acl/roles'));

    $response->assertUnauthorized();
});

it('should raise validation error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('acl/roles'));

    $response->assertJsonValidationErrors(['name', 'permissions']);
});

it('should raise forbidden on not having permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $data = [
        'name' => 'Test Role',
        'permissions' => [
            ProvidesTestingData::createPermissionRandomItem()->first()->id,
        ],
    ];

    $response = $this->jsonWithHeader('POST', $this->url('acl/roles'), $data);

    $response->assertForbidden();
});

it('should create role and assign permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['create'])],
    );
    $data = [
        'name' => 'Test Role',
        'permissions' => [
            ProvidesTestingData::createPermissionRandomItem()->first()->id,
        ],
    ];

    $response = $this->jsonWithHeader('POST', $this->url('acl/roles'), $data);

    $response->assertCreated();

    $response->assertJsonDataItemStructure($this->getRoleStructure([
        '[permissions:permission]',
    ]));
});
