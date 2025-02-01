<?php

declare(strict_types=1);

namespace App\Contracts\Services\User;

use App\Models\User\User;
use App\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

interface UserServiceContract
{
    public function findItems(
        array $filters = [],
        array $relations = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findById(int $id, array $relations = []): ?User;

    public function findRemovedItemByIdOrFail(int $id): User;

    public function store(array $data, array $relations = []): User;

    public function delete(User $user): bool;

    public function restore(User $user): User;

    public function getAcl(User $user): array;

    public function attachPermissions(array $permissions, User $user): SupportCollection;

    public function attachRoles(array $roles, User $user): Collection;

    public function passwordUpdate(array $data): bool;

    public function deactivate(User $user, string $reason): User;

    public function activate(User $user): User;
}
