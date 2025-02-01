<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\V1\Auth\ClientRepository;
use App\Repositories\V1\Auth\UsersRepository;
use App\Services\V1\Auth\AuthService;
use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\ClientRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\TokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use App\Support\Auth\Passport\PassportServiceProvider as BasePassportServiceProvider;
use App\Support\Auth\Passport\Repositories\RefreshTokenBridgeRepository;
use App\Support\Auth\Passport\Repositories\RefreshTokenRepository;
use App\Support\Auth\Passport\Repositories\TokenRepository;

class PassportServiceProvider extends BasePassportServiceProvider
{
    protected function registerCustomRepositories(): void
    {
        $this->app->bind(ClientRepositoryContract::class, ClientRepository::class);
        $this->app->bind(TokenRepositoryContract::class, TokenRepository::class);
        $this->app->bind(RefreshTokenBridgeRepositoryContract::class, RefreshTokenBridgeRepository::class);
        $this->app->bind(UserRepositoryContract::class, UsersRepository::class);
        $this->app->bind(RefreshTokenRepositoryContract::class, RefreshTokenRepository::class);
        $this->app->bind(AuthServiceContract::class, AuthService::class);
    }
}
