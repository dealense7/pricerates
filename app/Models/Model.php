<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Collection;
use App\Support\Resources\Contracts\TransformableContract;
use App\Support\Traits\HasDefaultDates;
use App\Support\Traits\Paginatable;
use App\Support\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Model extends BaseModel implements TransformableContract
{
    use HasFactory;
    use Sortable;
    use Paginatable;
    use HasDefaultDates;

    protected $perPage = 25;
    protected int $maxPerPage = 100;
    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    public static function getPermission(string $permission): string
    {
        return static::PERMISSIONS_SCOPE . '.' . $permission;
    }
}
