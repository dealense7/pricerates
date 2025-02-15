<?php

declare(strict_types=1);

namespace App\Models\Store;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property string name
 * @property boolean show
 */
class Store extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'name',
        'show',
    ];

    protected $casts = [
        'name' => 'string',
        'show' => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShow(): bool
    {
        return $this->show;
    }

    public function urls(): HasMany
    {
        return $this->hasMany(Url::class, 'store_id');
    }
}
