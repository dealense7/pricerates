<?php

declare(strict_types=1);

namespace App\Resources\Acl;

use App\Models\Acl\Permission;
use App\Support\Resources\JsonResource;

class PermissionResource extends JsonResource
{
    protected static array $transformMapping = [
        'name'         => 'name',
        'display_name' => 'displayName',
        'guard_name'   => 'guardName',
        'created_at'   => 'createdAt',
        'updated_at'   => 'updatedAt',
    ];

    public function __construct(?Permission $resource)
    {
        $this->resource = $resource;
    }
}
