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
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    $user = ProvidesTestingData::createRandomUserAndAuthorize(['company_id' => $company->id]);

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertForbidden();
});

it('should show own company colleague profile', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId(),],
        ['permissions' => $this->getPermissions(['read'])],
    );
    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers(['company_id' => $company->id])->first();
    ProvidesTestingData::createContactInformationRandomItem(['user_id' => $user->id]);

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertOk();

    $response->assertJsonDataItemStructure($this->getUserStructure(['company', '[contactInformation]']));
});

it('should not show user if is from other company', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId(),],
        ['permissions' => $this->getPermissions(['read'])],
    );
    /** @var \App\Models\Client\Company $company */
    $differentCompany = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers(['company_id' => $differentCompany->id])->first();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertNotFound();
});

it('should show user any user if can read everyone', function () {
    /** @var \App\Models\Client\Company $company */
    $company = ProvidesTestingData::createCompanyRandomItem()->first();
    ProvidesTestingData::createRandomUserAndAuthorize(
        ['company_id' => $company->getId(),],
        ['permissions' => $this->getPermissions(['read', 'read_everyone'])],
    );
    /** @var \App\Models\Client\Company $company */
    $differentCompany = ProvidesTestingData::createCompanyRandomItem()->first();
    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers(['company_id' => $differentCompany->id])->first();

    $response = $this->jsonWithHeader('GET', $this->url('users/' . $user->id));

    $response->assertJsonDataItemStructure(
        $this->getUserStructure(),
    );
});
