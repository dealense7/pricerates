<?php

declare(strict_types=1);

namespace App\Contracts\Services\Acl;

use App\Support\Collection;

interface PermissionServiceContract
{
    public function getAllItems(array $filters = []): Collection;
}
