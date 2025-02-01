<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\Auth\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('auth/acl'));

    $response->assertUnauthorized();
});


it('should return list', function () {
    $user = ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])]);

    /** @var \App\Models\Acl\Role $role */
    $role              = ProvidesTestingData::createRoleRandomItem()->first();
    $permissionForRole = ProvidesTestingData::createPermissionRandomItem();

    $role->permissions()->attach($permissionForRole->pluck('id')->toArray());

    $randomPermissionForUser = ProvidesTestingData::createPermissionRandomItem();
    $user->permissions()->attach($randomPermissionForUser->pluck('id')->toArray());
    $user->roles()->attach($role->pluck('id')->toArray());

    $response = $this->jsonWithHeader('GET', $this->url('auth/acl'));

    $response->assertJsonDataItemStructure([
        'attributes' => [
            'permissions',
            'roles',
        ],
    ]);
});
