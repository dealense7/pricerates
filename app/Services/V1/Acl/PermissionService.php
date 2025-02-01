<?php

declare(strict_types=1);

namespace App\Services\V1\Acl;

use App\Contracts\Repositories\Acl\PermissionRepositoryContract;
use App\Contracts\Services\Acl\PermissionServiceContract;
use App\Services\Service;
use App\Support\Collection;

class PermissionService extends Service implements PermissionServiceContract
{
    public function __construct(
        protected PermissionRepositoryContract $repository,
    ) {
        //
    }

    public function getAllItems(array $filters = []): Collection
    {
        return $this->repository->getAllItems($filters);
    }
}
