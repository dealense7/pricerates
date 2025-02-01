<?php

declare(strict_types=1);

namespace App\Contracts\Services\Acl;

use App\Models\Acl\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleServiceContract
{
    public function findItems(
        array $filters = [],
        int $page = 1,
        ?int $perPage = null,
        ?string $sort = null,
    ): LengthAwarePaginator;

    public function findByIdOrFail(int $id): Role;

    public function create(array $data): Role;

    public function update(array $data, Role $item): Role;

    public function delete(Role $item): bool;
}
