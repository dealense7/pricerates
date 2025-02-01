<?php

declare(strict_types=1);

use App\Models\Acl\Role;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\AclRoles\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('PUT', $this->url('acl/roles/10'));

    $response->assertUnauthorized();
});

it('should raise validation error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('PUT', $this->url('acl/roles/10'));

    $response->assertJsonValidationErrors(['name', 'permissions']);
});

it('should raise forbidden on not having permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read'])],
    );

    $data = [
        'name' => 'Test Role',
        'permissions' => [
            ProvidesTestingData::createPermissionRandomItem()->first()->id,
        ],
    ];

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem()->first();

    $response = $this->jsonWithHeader('PUT', $this->url('acl/roles/' . $role->getId()), $data);

    $response->assertForbidden();
});

it('should create role and assign permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read', 'update'])],
    );

    /** @var \App\Models\Acl\Role $role */
    $role = ProvidesTestingData::createRoleRandomItem()->first();
    $oldPermissionId = ProvidesTestingData::createPermissionRandomItem()->first()->id;
    $role->permissions()->attach([$oldPermissionId]);

    $newPermissionId = ProvidesTestingData::createPermissionRandomItem()->first()->id;
    $data = [
        'name' => 'Test Role',
        'permissions' => [$newPermissionId],
    ];

    $this->assertDatabaseHas(
        (new Role())->permissions()->getTable(),
        ['role_id' => $role->getId(), 'permission_id' => $oldPermissionId],
    );

    $response = $this->jsonWithHeader('PUT', $this->url('acl/roles/' . $role->getId()), $data);

    $response->assertOk();

    $this->assertDatabaseHas(
        (new Role())->permissions()->getTable(),
        ['role_id' => $role->getId(), 'permission_id' => $newPermissionId],
    );

    $this->assertDatabaseMissing(
        (new Role())->permissions()->getTable(),
        ['role_id' => $role->getId(), 'permission_id' => $oldPermissionId],
    );

    $response->assertJsonDataItemStructure($this->getRoleStructure([
        '[permissions:permission]',
    ]));
});
