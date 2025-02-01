<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Services;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\TokenRepositoryContract;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use App\Support\Auth\Passport\Guards\RequestGuard;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\ItemNotFoundException;
use InvalidArgumentException;

use function is_null;

class AuthService implements AuthServiceContract
{
    public function __construct(
        protected RefreshTokenRepositoryContract $refreshTokenRepositoryContract,
        protected TokenRepositoryContract $tokenRepositoryContract,
        protected UserRepositoryContract $userRepositoryContract,
        protected AuthManager $authManager,
        protected HasherContract $hasher,
        protected Dispatcher $dispatcher,
    ) {
        //
    }

    public function findOneForAuth(int $id): ?UserContract
    {
        return $this->userRepositoryContract->findOneForAuth($id);
    }

    public function findOneForAuthOrFail(int $id): UserContract
    {
        $item = $this->findOneForAuth($id);
        if (! $item) {
            throw new ItemNotFoundException();
        }

        return $item;
    }

    public function retrieveUserById(int $id): ?UserContract
    {
        return $this->findOneForAuth($id);
    }

    public function updateAccessToken(string $accessTokenIdentifier, int $userId): void
    {
        $this->tokenRepositoryContract->update($accessTokenIdentifier, $userId);
    }

    public function updateRememberToken(UserContract $user, string $token): void
    {
        $this->userRepositoryContract->updateRememberToken($user, $token);
    }

    public function revokeToken(): void
    {
        if (! $this->authManager->guard() instanceof RequestGuard) {
            throw new InvalidArgumentException('Current guard is not the request guard');
        }

        if (is_null($this->authManager->user()->token())) {
            return;
        }

        /** @var \App\Support\Auth\Passport\Contracts\UserContract $user */
        $user = $this->authManager->user();

        $token = $user->token();
        $tokenId = $token->getKey();

        $token->revoke();

        $this->refreshTokenRepositoryContract->revokeRefreshTokensByAccessTokenId($tokenId);

        $this->unsetUser();
    }

    public function revokeOtherTokens(): void
    {
        if (! $this->authManager->guard() instanceof RequestGuard) {
            throw new InvalidArgumentException('Current guard is not request guard');
        }

        $user = $this->getUser();
        if (is_null($user->tokens)) {
            return;
        }

        /** @var \Laravel\Passport\Token $currentToken */
        $currentToken = $user->token();

        /** @var \Laravel\Passport\Token $token */
        foreach ($user->tokens as $token) {
            if ($currentToken->getKey() === $token->getKey()) {
                continue;
            }

            $tokenId = $token->getKey();
            $token->revoke();
            $this->refreshTokenRepositoryContract->revokeRefreshTokensByAccessTokenId($tokenId);
        }
    }

    public function unsetUser(): void
    {
        $this->authManager->unsetUser();
    }

    public function getUser(): ?UserContract
    {
        /** @var \App\Models\User\User $user */
        $user = $this->authManager->user();

        return $user;
    }

    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        $user = $this->userRepositoryContract->retrieveByCredentials($credentials);
        if (! $user) {
            return null;
        }

        return $user;
    }

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract
    {
        return $this->userRepositoryContract->retrieveUserByToken($identifier, $token);
    }

    public function validateCredentials(UserContract $user, string $password): bool
    {
        return $this->hasher->check($password, $user->getAuthPassword());
    }

    public function fireLoginEvent(string $guard, UserContract $user, bool $remember = false): void
    {
        $this->dispatcher->dispatch(new Login($guard, $user, $remember));
    }
}
