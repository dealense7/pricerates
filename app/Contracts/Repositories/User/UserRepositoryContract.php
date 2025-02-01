<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    public function findItems(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findById(int $id, array $relations = [], array $filters = []): ?User;

    public function findRemovedItemByIdOrFail(int $id): ?User;

    public function store(array $data, array $relations = []): User;

    public function update(User $item, array $data, array $relations = []): User;

    public function forceFill(User $item, array $data): bool;

    public function delete(User $item): void;

    public function restore(User $item): User;

    public function attachPermissions(array $permissions, User $item): User;

    public function attachRoles(array $roles, User $item): User;
}
