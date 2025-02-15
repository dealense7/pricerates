<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property string name
 * @property string slug
 * @property boolean show
 * @property integer parent_id
 */
class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'show',
        'parent_id',
    ];

    protected $casts = [
        'name'      => 'string',
        'slug'      => 'string',
        'show'      => 'boolean',
        'parent_id' => 'integer',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getShow(): bool
    {
        return $this->show;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
