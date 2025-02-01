<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('acl/permissions'));

    $response->assertUnauthorized();
});

it('should return list', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    ProvidesTestingData::createPermissionRandomItem();

    $response = $this->jsonWithHeader('GET', $this->url('acl/permissions'));

    $response->assertOk();

    $response->assertJsonDataCollectionStructure($this->getPermissionStructure(), false);
});
