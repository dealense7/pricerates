<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('POST', $this->url('users/activate/10'));

    $response->assertUnauthorized();
});

it('should raise not found error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $data = ['reason' => 'not-found'];

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/10'), $data);

    $response->assertNotFound();
});

it('should raise forbidden on permission', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['read', 'read_everyone']),
    ]);

    $user = ProvidesTestingData::createRandomUsers(['deactivated_at' => now()])->first();
    $data = ['reason' => 'not-found'];

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/' . $user->getId()), $data);

    $response->assertForbidden();
});

it('should not found other company user', function () {

    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        [
            'company_id' => $company->getId(),
        ],
        [
            'permissions' => $this->getPermissions(['activate', 'read']),
        ],
    );

    $data = ['reason' => 'not-found'];

    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/' . $user->getId()), $data);

    $response->assertNotFound();
});

it('should raise error for activate himself', function () {
    $user = ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['activate', 'read', 'read_everyone']),
    ]);

    $data = ['reason' => 'not-found'];

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/' . $user->getId()), $data);

    $response->assertForbidden();
});

it('should raise error for already activated', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['activate', 'read', 'read_everyone']),
    ]);

    $data = ['reason' => 'not-found'];

    $user = ProvidesTestingData::createRandomUsers([
        'deactivated_at' => null,
    ])->first();

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/' . $user->getId()), $data);

    $response->assertForbidden();
});

it('should activate user', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], [
        'permissions' => $this->getPermissions(['activate', 'read', 'read_everyone']),
    ]);

    $data = ['reason' => 'not-found'];

    $user = ProvidesTestingData::createRandomUsers([
        'deactivated_at' => now(),
    ])->first();

    $response = $this->jsonWithHeader('POST', $this->url('users/activate/' . $user->getId()), $data);

    $response->assertOk();
});
