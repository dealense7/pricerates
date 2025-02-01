<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \App\Models\User\User
 */
trait UserHasPermissions
{
    use HasRoles;

    public function addUserPermissions(iterable $permissions): void
    {
        $this->permissions()->attach(collect($permissions)->pluck('id')->toArray());
    }

    public function addUserRoles(iterable $roles): void
    {
        $this->roles()->attach(collect($roles)->pluck('id')->toArray());
    }
}
