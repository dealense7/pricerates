<?php

declare(strict_types=1);

namespace App\Services\V1\Auth;

use App\Models\User\User;
use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Auth\Passport\Services\AuthService as BaseAuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

use function app;

class AuthService extends BaseAuthService implements AuthServiceContract
{
    use AuthorizesRequests;

    public function attempt(array $credentials, bool $remember = false): bool
    {
        /** @see \Illuminate\Auth\SessionGuard::attempt() */
        return $this->authManager->attempt($credentials, $remember);
    }

    public function findOneForAuth(int $id): ?UserContract
    {
        return $this->userRepositoryContract->findOneForAuth($id);
    }

    public function logout(): void
    {
        /** @see \Illuminate\Auth\SessionGuard::logout() */
        $this->authManager->logout();
    }

    public function loginUsingId(int $id, bool $remember = false): ?Authenticatable
    {
        /** @see \Illuminate\Auth\SessionGuard::loginUsingId() */
        return $this->authManager->loginUsingId($id, $remember);
    }

    public function login(Authenticatable $user, bool $remember = false): void
    {
        /** @see \Illuminate\Auth\SessionGuard::login() */
        $this->authManager->login($user, $remember);
    }

    public function resetPassword(array $credentials): string
    {
        $response = $this->broker()->reset(
            $credentials,
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => $this->hasher->make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $this->usersService->clearAuthCache($user);
            },
        );

        return $response;
    }

    public function sendPasswordResetLink(array $credentials): string
    {
        $response = $this->broker()->sendResetLink($credentials);

        return $response;
    }

    public function broker(): PasswordBroker
    {
        return app(PasswordBroker::class);
    }
}
