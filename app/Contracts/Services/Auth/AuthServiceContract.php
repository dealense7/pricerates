<?php

declare(strict_types=1);

namespace App\Contracts\Services\Auth;

use App\Support\Auth\Passport\Contracts\AuthServiceContract as BaseAuthServiceContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;

interface AuthServiceContract extends BaseAuthServiceContract
{
    public function attempt(array $credentials, bool $remember = false): bool;

    public function loginUsingId(int $id, bool $remember = false): ?Authenticatable;

    public function login(Authenticatable $user, bool $remember = false): void;

    public function logout(): void;

    public function resetPassword(array $credentials): string;

    public function sendPasswordResetLink(array $credentials): string;

    public function broker(): PasswordBroker;
}
