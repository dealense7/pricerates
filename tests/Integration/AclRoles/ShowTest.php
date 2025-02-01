<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('acl/roles/10'));

    $response->assertUnauthorized();
});

it('should raise forbidden on not having permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem()->first();

    $response = $this->jsonWithHeader('GET', $this->url('acl/roles/' . $role->getId()));

    $response->assertForbidden();
});

it('should create role and assign permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read'])],
    );

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem()->first();
    $oldPermissionId = ProvidesTestingData::createPermissionRandomItem()->first()->id;
    $role->permissions()->attach([$oldPermissionId]);

    $response = $this->jsonWithHeader('GET', $this->url('acl/roles/' . $role->getId()));

    $response->assertOk();

    $response->assertJsonDataItemStructure($this->getRoleStructure([
        '[permissions:permission]',
    ]));
});
