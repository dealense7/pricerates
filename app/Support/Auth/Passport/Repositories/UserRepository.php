<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Models\User\User;
use App\Support\Auth\Passport\Contracts\UserContract;
use App\Support\Auth\Passport\Contracts\UserRepositoryContract;
use Exception;
use Illuminate\Database\Connection;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use RuntimeException;

class UserRepository implements UserRepositoryContract
{
    protected Connection $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function findOneForAuth(int $id): ?UserContract
    {
        $item = $this->getModel()
            ->find($id);

        return $item;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity,
    ) {
        throw new Exception('getUserEntityByUserCredentials is deprecated!');
    }

    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        if (empty($credentials)) {
            return null;
        }

        $login = $credentials['login'] ?? $credentials['email'];

        $query = $this->getModel()->newQuery();

        $query->where('email', '=', $login);

        /** @var \App\Support\Auth\Passport\Contracts\UserContract $user */
        $user = $query->first();

        if (! $user) {
            return null;
        }

        return $user;
    }

    public function retrieveUserByToken(int $identifier, string $token): ?UserContract
    {
        throw new RuntimeException('Not implemented');
    }

    public function updateRememberToken(UserContract $user, string $token): void
    {
        // Not used
    }

    protected function getModel(): User
    {
        return new User();
    }
}
