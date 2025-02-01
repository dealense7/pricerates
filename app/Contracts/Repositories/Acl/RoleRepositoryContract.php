<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Acl;

use App\Models\Acl\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryContract
{
    public function findItems(
        array $filters = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findById(int $id): ?Role;

    public function create(array $data): Role;

    public function update(array $data, Role $item): Role;

    public function attachPermissions(array $permissions, Role $item): Role;

    public function delete(Role $item): void;
}
