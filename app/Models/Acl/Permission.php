<?php

declare(strict_types=1);

namespace App\Models\Acl;

use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Traits\Paginatable;
use App\Support\Traits\Sortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as BasePermission;

/**
 * @property string display_name
 */
class Permission extends BasePermission implements TransformableContract
{
    use HasFactory;
    use Paginatable;
    use Sortable;

    protected $table = 'permissions';

    public function getId(): int
    {
        return $this->id;
    }

    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
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
}
