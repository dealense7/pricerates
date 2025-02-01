<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('POST', $this->url('users/restore/10'));

    $response->assertUnauthorized();
});

it('should raise not found error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('users/restore/10'));

    $response->assertNotFound();
});

it('should raise forbidden', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read']),
    ]);

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'deleted_at' => now(),
    ], [], 1)->first();

    $response = $this->jsonWithHeader('POST', $this->url('users/restore/' . $user->getId()));

    $response->assertForbidden();
});

it('should delete user', function () {
    $mainUser = ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['restore', 'read']),
    ]);

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers(['deleted_at' => now(), 'deleted_by' => $mainUser->id])->first();

    $this->assertSoftDeleted(
        $user->getTable(),
        [
            'id' => $user->getId(),
        ],
    );

    $response = $this->jsonWithHeader('POST', $this->url('users/restore/' . $user->getId()));

    $response->assertOk();


    $response->assertJsonDataItemStructure(
        $this->getUserStructure(),
    );

    $this->assertNotSoftDeleted(
        $user->getTable(),
        [
            'id'         => $user->getId(),
            'deleted_by' => null,
        ],
    );
});
