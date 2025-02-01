<?php

declare(strict_types=1);

use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\Integration\Auth\ModelTestCase;

uses(ModelTestCase::class);
uses(DatabaseTransactions::class);


it('should return error on invalid client', function () {

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'          => ProvidesTestingData::getFaker()->email,
        'password'       => bcrypt('12345678'),
        'uses_otp_check' => true,
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => Str::uuid()->toString(),
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(401);
});


it('should return error on invalid grant', function () {
    $OauthClient = ProvidesTestingData::createRandomOauthClient();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'          => ProvidesTestingData::getFaker()->email,
        'password'       => bcrypt('12345678'),
        'uses_otp_check' => true,
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => $OauthClient->id,
        'grant_type' => 'wrong_grant',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(400);
});

it('should return error on refresh token if not verified', function () {
    $OauthClient = ProvidesTestingData::createRandomOauthClient();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'          => ProvidesTestingData::getFaker()->email,
        'password'       => bcrypt('12345678'),
        'uses_otp_check' => true,
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => $OauthClient->id,
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure($this->getAccessTokenStructure());

    $content = $response->getDecodedContent();

    // Get access token by refresh token
    $refreshToken = $content['refresh_token'];
    $response     = $this->json('POST', $this->url('auth/token'), [
        'client_id'     => $OauthClient->id,
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => $refreshToken,
    ]);

    $response->assertStatus(401);
});

it('should raise error on endpoints if otp if not valid', function () {
    $OauthClient = ProvidesTestingData::createRandomOauthClient();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'          => ProvidesTestingData::getFaker()->email,
        'password'       => bcrypt('12345678'),
        'uses_otp_check' => true,
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => $OauthClient->id,
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure($this->getAccessTokenStructure());

    $content = $response->getDecodedContent();
    // Get user data by access token
    $accessToken = $content['access_token'];

    $response = $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer ' . $accessToken,
    ]);

    $response->assertStatus(401);
});

it('should return user by internal grant', function () {
    $OauthClient = ProvidesTestingData::createRandomOauthClient();

    /** @var \App\Models\User\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'    => ProvidesTestingData::getFaker()->email,
        'password' => bcrypt('12345678'),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => $OauthClient->id,
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '12345678',
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure($this->getAccessTokenStructure());

    $content = $response->getDecodedContent();

    // Get access token by refresh token
    $refreshToken = $content['refresh_token'];
    $response     = $this->json('POST', $this->url('auth/token'), [
        'client_id'     => $OauthClient->id,
        'grant_type'    => 'internal_refresh_token',
        'refresh_token' => $refreshToken,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure($this->getAccessTokenStructure());

    $content = $response->getDecodedContent();

    // Get user data by access token
    $accessToken = $content['access_token'];

    $response = $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer ' . $accessToken,
    ]);

    $response->assertStatus(200);
    $response->assertJsonDataItemStructure($this->getUserStructure());

    // Logout
    $response = $this->json('DELETE', $this->url('auth/token'), [], [
        'Authorization' => 'Bearer ' . $accessToken,
    ]);
    $response->assertOk();

    $this->assertDatabaseMissing('oauth_access_tokens', [
        'user_id' => $user->id,
        'revoked' => false,
    ]);

    $response = $this->json('GET', $this->url('auth/me'), [], [
        'Authorization' => 'Bearer ' . $accessToken,
    ]);

    $response->assertUnauthorized();
});

it('should return error with wrong password', function () {
    $client = ProvidesTestingData::createRandomOauthClient();

    /** @var \App\Models\User $user */
    $user = ProvidesTestingData::createRandomUsers([
        'email'    => ProvidesTestingData::getFaker()->email,
        'password' => bcrypt('12345678'),
    ])->first();

    $response = $this->json('POST', $this->url('auth/token'), [
        'client_id'  => $client->id,
        'grant_type' => 'internal',
        'login'      => $user->getEmail(),
        'password'   => '123456789', // wrong password
    ]);

    $response->assertStatus(400);
    $response->assertJsonFragment([
        'error'             => 'invalid_grant',
        'error_description' => 'The user credentials were incorrect.',
        'message'           => 'The user credentials were incorrect.',
    ]);
});
