<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('GET', $this->url('users/10'));

    $response->assertUnauthorized();
});

it('should raise not found', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('users/10'));

    $response->assertNotFound();
});

it('should raise forbidden for permission', function () {
    $user = ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertForbidden();
});

it('should show user', function () {
    ProvidesTestingData::createRandomUserAndAuthorize(
        [],
        ['permissions' => $this->getPermissions(['read'])],
    );

    $user = ProvidesTestingData::createRandomUsers()->first();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertJsonDataItemStructure(
        $this->getUserStructure(),
    );
});
