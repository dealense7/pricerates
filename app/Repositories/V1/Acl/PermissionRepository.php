<?php

declare(strict_types=1);

namespace App\Repositories\V1\Acl;

use App\Contracts\Repositories\Acl\PermissionRepositoryContract;
use App\Filters\FilterByName;
use App\Models\Acl\Permission;
use App\Repositories\Repository;
use App\Support\Collection;

class PermissionRepository extends Repository implements PermissionRepositoryContract
{
    public function getAllItems(array $filters = []): Collection
    {
        /** @var \App\Support\Collection $items */
        $items = $this->getData($filters)->get();

        return $items;
    }

    public function getModel(): Permission
    {
        return new Permission();
    }

    public function getFilters(): array
    {
        return [
            FilterByName::class,
        ];
    }
}
