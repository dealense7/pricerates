<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('DELETE', $this->url('users/10'));

    $response->assertUnauthorized();
});

it('should raise not found error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('DELETE', $this->url('users/789'));

    $response->assertNotFound();
});

it('should raise forbidden on permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);

    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('users/' . $user->getId()));

    $response->assertForbidden();
});

it('should raise not found on suicide', function () {
    $user = ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['delete', 'read']),
    ]);

    $response = $this->jsonWithHeader('DELETE', $this->url('users/' . $user->getId()));

    $response->assertForbidden();
});

it('should delete user', function () {
    $mainUser = ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['delete', 'read']),
    ]);

    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('DELETE', $this->url('users/' . $user->getId()));

    $response->assertOk();

    $this->assertSoftDeleted(
        $user->getTable(),
        [
            'id'         => $user->getId(),
            'deleted_by' => $mainUser->getId(),
        ],
    );
});
