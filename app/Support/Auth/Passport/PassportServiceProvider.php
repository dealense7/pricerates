<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\ClientRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\TokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use App\Support\Auth\Passport\Grants\InternalGrant;
use App\Support\Auth\Passport\Grants\InternalRefreshTokenGrant;
use App\Support\Auth\Passport\Guards\RequestGuard;
use App\Support\Auth\Passport\Guards\TokenGuard;
use App\Support\Auth\Passport\Repositories\ClientRepository;
use App\Support\Auth\Passport\Repositories\RefreshTokenBridgeRepository;
use App\Support\Auth\Passport\Repositories\RefreshTokenRepository;
use App\Support\Auth\Passport\Repositories\TokenRepository;
use App\Support\Auth\Passport\Repositories\UserRepository;
use DateInterval;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use Laravel\Passport\PassportUserProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;

use function tap;

class PassportServiceProvider extends BasePassportServiceProvider
{
    public function register(): void
    {
        Passport::ignoreRoutes();

        parent::register();

        $this->registerCustomRepositories();
    }

    protected function registerAuthorizationServer(): void
    {
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap($this->makeAuthorizationServer(), function (AuthorizationServer $server) {
                $accessTokenTtl = new DateInterval('PT1H');

                $server->setDefaultScope(Passport::$defaultScope);

                $server->enableGrantType($this->makeInternalGrant(), $accessTokenTtl);

                $server->enableGrantType($this->makeInternalRefreshTokenGrant(), $accessTokenTtl);
            });
        });
    }

    protected function makeGuard(array $config): RequestGuard
    {
        return new RequestGuard(function (Request $request) use ($config) {
            /** @var \Illuminate\Auth\AuthManager $authManager */
            $authManager = $this->app['auth'];

            return (new TokenGuard(
                $this->app->make(ResourceServer::class),
                new PassportUserProvider($authManager->createUserProvider($config['provider']), $config['provider']),
                $this->app->make(TokenRepositoryContract::class),
                $this->app->make(ClientRepositoryContract::class),
                $this->app->make('encrypter'),
                $request,
            ))->user();
        }, $this->app['request']);
    }

    protected function registerCustomRepositories(): void
    {
        $this->app->bind(ClientRepositoryContract::class, ClientRepository::class);
        $this->app->bind(TokenRepositoryContract::class, TokenRepository::class);
        $this->app->bind(RefreshTokenBridgeRepositoryContract::class, RefreshTokenBridgeRepository::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(RefreshTokenRepositoryContract::class, RefreshTokenRepository::class);
        $this->app->bind(AuthServiceContract::class, AuthServiceContract::class);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeInternalGrant(): InternalGrant
    {
        $grant = new InternalGrant(
            $this->app->make(AuthServiceContract::class),
            $this->app->make(RefreshTokenBridgeRepositoryContract::class),
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1Y'));

        return $grant;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeInternalRefreshTokenGrant(): InternalRefreshTokenGrant
    {
        $repository = $this->app->make(RefreshTokenBridgeRepositoryContract::class);

        $grant = new InternalRefreshTokenGrant($repository);

        $grant->setRefreshTokenTTL(new DateInterval('P1Y'));

        return $grant;
    }
}
