<?php

declare(strict_types=1);

namespace App\Resources\Acl;

use App\Models\Acl\Role;
use App\Support\Resources\JsonResource;
use App\Support\Resources\JsonResourceCollection;

class RoleResource extends JsonResource
{
    protected static array $transformMapping = [
        'name'         => 'name',
        'display_name' => 'displayName',
        'guard_name'   => 'guardName',
        'is_custom'    => 'isCustom',
        'created_at'   => 'createdAt',
        'updated_at'   => 'updatedAt',
    ];

    public function __construct(?Role $resource)
    {
        $this->resource = $resource;
    }

    public function includePermissions(): JsonResourceCollection
    {
        return new JsonResourceCollection($this->whenLoaded('permissions'), PermissionResource::class);
    }
}
