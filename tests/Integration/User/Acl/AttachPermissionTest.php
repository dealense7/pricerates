<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('PUT', $this->url('users/10/permissions'));

    $response->assertUnauthorized();
});

it('should raise not found', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $permissions = ProvidesTestingData::createPermissionRandomItem();

    $data = [
        'permissions' => $permissions->pluck('id')->toArray(),
    ];

    $response = $this->jsonWithHeader('PUT', $this->url('users/10/permissions'), $data);

    $response->assertNotFound();
});

it('should raise forbidden', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $permissions = ProvidesTestingData::createPermissionRandomItem();

    $data = ['permissions' => $permissions->pluck('id')->toArray(),];

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('PUT', $this->url('users/' . $user->getId() . '/permissions'), $data);

    $response->assertForbidden();
});

it('should sync permissions', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'read_everyone', 'update']),
    ]);

    /** @var \App\Models\User\User $newPermission */
    $newPermission = ProvidesTestingData::createPermissionRandomItem()->first();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    /** @var \App\Models\Acl\Permission $removePermissions */
    $removePermissions = ProvidesTestingData::createPermissionRandomItem()->first();

    /** @var \App\Models\Acl\Permission $stayPermissions */
    $stayPermissions = ProvidesTestingData::createPermissionRandomItem()->first();

    $user->permissions()->attach([
        $removePermissions->getId(),
        $stayPermissions->getId(),
    ]);

    $data = [
        'permissions' => [
            $newPermission->getId(),
            $stayPermissions->getId(),
        ],
    ];

    $this->assertDatabaseHas(
        $user->permissions()->first()->pivot->getTable(),
        [
            'permission_id' => $removePermissions->getId(),
        ],
    );

    $this->assertDatabaseMissing(
        $user->permissions()->first()->pivot->getTable(),
        [
            'model_id'      => $user->getId(),
            'permission_id' => $newPermission->getId(),
        ],
    );

    $response = $this->jsonWithHeader('PUT', $this->url('users/' . $user->getId() . '/permissions'), $data);

    $response->assertOk();

    $this->assertDatabaseMissing(
        $user->permissions()->first()->pivot->getTable(),
        [
            'permission_id' => $removePermissions->getId(),
        ],
    );

    $this->assertDatabaseHas(
        $user->permissions()->first()->pivot->getTable(),
        [
            'model_id'      => $user->getId(),
            'permission_id' => $newPermission->getId(),
        ],
    );

    $response->assertJsonDataCollectionStructure($this->getPermissionStructure(), false);
});
