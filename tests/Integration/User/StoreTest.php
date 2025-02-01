<?php

declare(strict_types=1);

use App\Models\User\ContactInformation;
use App\Models\User\User;
use App\Resources\User\UserResource;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\User\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('POST', $this->url('users'));

    $response->assertUnauthorized();
});

it('should raise validation error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('users'));

    $response->assertJsonValidationErrors([
        'username',
        'email',
    ]);
});

it('should raise validation error on client if user is super admin', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('POST', $this->url('users'));

    $response->assertJsonValidationErrors([
        'username',
        'email',
    ]);
});

it('should raise forbidden error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $data = [
        'username'    => ProvidesTestingData::getFaker()->name,
        'firstName'   => ProvidesTestingData::getFaker()->firstName,
        'lastName'    => ProvidesTestingData::getFaker()->lastName,
        'email'       => ProvidesTestingData::getFaker()->email,
        'phoneNumber' => ProvidesTestingData::getFaker()->phoneNumber,
        'companyId'   => 10,
    ];

    $response = $this->jsonWithHeader('POST', $this->url('users'), $data);

    $response->assertForbidden();
});

it('should raise validation error on company if moderator adds user', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);

    $data = [
        'username'    => ProvidesTestingData::getFaker()->name,
        'firstName'   => ProvidesTestingData::getFaker()->firstName,
        'lastName'    => ProvidesTestingData::getFaker()->lastName(),
        'email'       => ProvidesTestingData::getFaker()->email,
        'phoneNumber' => ProvidesTestingData::getFaker()->phoneNumber,
    ];

    $response = $this->jsonWithHeader('POST', $this->url('users'), $data);

    $response->assertJsonValidationErrors(['companyId']);
});


it('should store user', function () {
    ProvidesTestingData::createRandomUserAndAuthorize([], ['permissions' => $this->getPermissions(['create'])]);

    $data = [
        'username'    => ProvidesTestingData::getFaker()->name,
        'firstName'   => ProvidesTestingData::getFaker()->firstName,
        'lastName'    => ProvidesTestingData::getFaker()->lastName(),
        'email'       => ProvidesTestingData::getFaker()->email,
        'phoneNumber' => ProvidesTestingData::getFaker()->phoneNumber,
        'companyId'   => ProvidesTestingData::createCompanyRandomItem()->first()->id,
    ];

    $response = $this->jsonWithHeader('POST', $this->url('users'), $data);

    $response->assertCreated();

    $response->assertJsonDataItemStructure(
        $this->getUserStructure(),
    );

    // first item is authorized one and second is that we created
    $this->assertDatabaseCount(
        (new User())->getTable(),
        2,
    );

    $this->assertDatabaseCount(
        (new ContactInformation())->getTable(),
        2,
    );

    $this->assertDatabaseHas(
        (new User())->getTable(),
        UserResource::transformToInternal($data),
    );
});


it('should store user and assign own company', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->id],
        ['permissions' => $this->getPermissions(['create'])],
    );

    $data = [
        'username'    => ProvidesTestingData::getFaker()->name,
        'firstName'   => ProvidesTestingData::getFaker()->firstName,
        'lastName'    => ProvidesTestingData::getFaker()->lastName(),
        'email'       => ProvidesTestingData::getFaker()->email,
        'phoneNumber' => ProvidesTestingData::getFaker()->phoneNumber,
    ];

    $response = $this->jsonWithHeader('POST', $this->url('users'), $data);

    $response->assertCreated();

    $response->assertJsonDataItemStructure(
        $this->getUserStructure(),
    );

    // first item is authorized one and second is that we created
    $this->assertDatabaseCount(
        (new User())->getTable(),
        2,
    );
    $this->assertDatabaseHas(
        (new User())->getTable(),
        [
            'id'         => $response->getDecodedContent()['data']['id'],
            'company_id' => $company->id,
        ],
    );

    $this->assertDatabaseCount(
        (new ContactInformation())->getTable(),
        2,
    );

    $this->assertDatabaseHas(
        (new User())->getTable(),
        UserResource::transformToInternal($data),
    );
});
