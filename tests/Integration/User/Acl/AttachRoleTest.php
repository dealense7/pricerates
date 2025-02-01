<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('PUT', $this->url('users/10/roles'));

    $response->assertUnauthorized();
});

it('should raise validation error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('PUT', $this->url('users/10/roles'));

    $response->assertJsonValidationErrors([
        'roles',
    ]);
});

it('should raise not found', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $roles = ProvidesTestingData::createRoleRandomItem();

    $data = [
        'roles' => $roles->pluck('id')->toArray(),
    ];

    $response = $this->jsonWithHeader('PUT', $this->url('users/10/roles'), $data);

    $response->assertNotFound();
});

it('should raise not found on user from other company', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->id,],
        ['permissions' => $this->getPermissions(['read']),],
    );

    $roles = ProvidesTestingData::createRoleRandomItem();

    $data = [
        'roles' => $roles->pluck('id')->toArray(),
    ];

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('PUT', $this->url('users/' . $user->getId() . '/roles'), $data);

    $response->assertNotFound();
});

it('should raise forbidden', function () {

    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->id,],
        ['permissions' => $this->getPermissions(['read']),],
    );

    $roles = ProvidesTestingData::createRoleRandomItem();

    $data = ['roles' => $roles->pluck('id')->toArray(),];

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'company_id' => $company->id,
    ])->first();

    $response = $this->jsonWithHeader('PUT', $this->url('users/' . $user->getId() . '/roles'), $data);

    $response->assertForbidden();
});

it('should sync roles', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'read_everyone', 'update']),
    ]);

    /** @var \App\Models\Acl\Role $newRole */
    $newRole = ProvidesTestingData::createRoleRandomItem()->first();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    /** @var \App\Models\Acl\Role $removeRole */
    $removeRole = ProvidesTestingData::createRoleRandomItem()->first();

    /** @var \App\Models\Acl\Role $stayRole */
    $stayRole = ProvidesTestingData::createRoleRandomItem()->first();

    $user->roles()->attach([
        $removeRole->getId(),
        $stayRole->getId(),
    ]);

    $data = [
        'roles' => [
            $newRole->getId(),
            $stayRole->getId(),
        ],
    ];

    $this->assertDatabaseHas(
        $user->roles()->first()->pivot->getTable(),
        [
            'role_id' => $removeRole->getId(),
        ],
    );

    $this->assertDatabaseMissing(
        $user->roles()->first()->pivot->getTable(),
        [
            'model_id' => $user->getId(),
            'role_id'  => $newRole->getId(),
        ],
    );

    $response = $this->jsonWithHeader('PUT', $this->url('users/' . $user->getId() . '/roles'), $data);

    $response->assertOk();

    $this->assertDatabaseMissing(
        $user->roles()->first()->pivot->getTable(),
        [
            'role_id' => $removeRole->getId(),
        ],
    );

    $this->assertDatabaseHas(
        $user->roles()->first()->pivot->getTable(),
        [
            'model_id' => $user->getId(),
            'role_id'  => $newRole->getId(),
        ],
    );

    $response->assertJsonDataCollectionStructure($this->getRoleStructure(), false);
});
