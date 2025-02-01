<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Integration\Auth\ModelTestCase;

use function PHPUnit\Framework\assertTrue;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);

it('should raise unauthorized', function () {
    $response = $this->jsonWithHeader('PUT', $this->url('auth/password/update'));

    $response->assertUnauthorized();
});

it('should raise validation error', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $response = $this->jsonWithHeader('PUT', $this->url('auth/password/update'));

    $response->assertJsonValidationErrors([
        'currentPassword',
        'newPassword',
        'confirmPassword',
    ]);
});

it('should validation error if confirm password does not matches', function () {
    ProvidesTestingData::createRandomUserAndAuthorize();

    $data = [
        'currentPassword' => 'test',
        'newPassword'     => 'test',
        'confirmPassword' => 'test1',
    ];

    $response = $this->jsonWithHeader('PUT', $this->url('auth/password/update'), $data);

    $response->assertJsonValidationErrors([
        'confirmPassword',
    ]);
});

it('should update password', function () {
    $user = ProvidesTestingData::createRandomUserAndAuthorize([
        'password' => Hash::make('test'),
    ]);

    $newPassword = Str::password();

    $data = [
        'currentPassword' => 'test',
        'newPassword'     => $newPassword,
        'confirmPassword' => $newPassword,
    ];

    $response = $this->jsonWithHeader('PUT', $this->url('auth/password/update'), $data);

    $response->assertOk();

    $user->refresh();

    assertTrue(Hash::check($newPassword, $user->password));
});
