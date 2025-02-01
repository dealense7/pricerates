<?php

declare(strict_types=1);

namespace App\Contracts\Repositories\Acl;

use App\Support\Collection;

interface PermissionRepositoryContract
{
    public function getAllItems(array $filters = []): Collection;
}
