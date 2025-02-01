<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('users/10/acl'));

    $response->assertUnauthorized();
});

it('should raise not found on user', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('users/10/acl'));

    $response->assertNotFound();
});

it('should raise forbidden on return items list for permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->getId() . '/acl'));

    $response->assertForbidden();
});

it('should return list', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['read'])],);

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->getId() . '/acl'));

    $response->assertJsonDataItemStructure([
        'attributes' => [
            'permissions',
            'roles',
        ],
    ]);
});
