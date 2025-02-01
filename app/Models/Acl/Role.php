<?php

declare(strict_types=1);

namespace App\Models\Acl;

use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Traits\Paginatable;
use App\Support\Traits\Sortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

/**
 * @property string display_name
 * @property boolean is_custom
 */
class Role extends BaseRole implements TransformableContract
{
    use HasFactory;
    use Paginatable;
    use Sortable;

    public const PERMISSIONS_SCOPE = 'roles';

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    protected $perPage = 25;
    protected int $maxPerPage = 100;

    protected $table = 'roles';

    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIsCustom(): bool
    {
        return $this->is_custom;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function getGuardName(): string
    {
        return $this->guard_name;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public static function getPermission(string $permission): string
    {
        return static::PERMISSIONS_SCOPE . '.' . $permission;
    }
}
