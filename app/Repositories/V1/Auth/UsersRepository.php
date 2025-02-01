<?php

declare(strict_types=1);

namespace App\Repositories\V1\Auth;

use App\Contracts\Models\User\UserContract;
use App\Contracts\Repositories\Auth\UsersRepositoryContract;
use App\Models\User\User;
use App\Support\Auth\Passport\Repositories\UserRepository as BaseUserRepository;

class UsersRepository extends BaseUserRepository implements UsersRepositoryContract
{
    public function findOneForAuth(int $id): ?UserContract
    {
        /** @var \App\Models\User\User $item */
        $item = $this->getModel()->find($id);

        return $item;
    }

    protected function getModel(): User
    {
        return new User();
    }
}
