<?php

declare(strict_types=1);

use App\Models\Acl\Role;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('DELETE', $this->url('acl/roles/10'));

    $response->assertUnauthorized();
});

it('should raise forbidden on not having permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read'])],
    );

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem()->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('acl/roles/' . $role->getId()));

    $response->assertForbidden();
});

it('should raise error for deleting not custom', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read', 'delete'])],
    );

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem([
        'is_custom' => false,
    ])->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('acl/roles/' . $role->getId()));

    $response->assertForbidden();
});

it('should create role and assign permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read', 'delete'])],
    );

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem([
        'is_custom' => true,
    ])->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('acl/roles/' . $role->getId()));

    $response->assertOk();

    $this->assertDatabaseMissing((new Role())->getTable(), ['id' => $role->getId()]);
});
