<?php

declare(strict_types=1);

namespace App\Models\Products;

use App\Models\General\Category;
use App\Models\General\File;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int id
 * @property string name
 * @property string slug
 * @property string barcode
 * @property integer category_id
 * @property boolean show
 */
class Item extends Model
{
    protected $table = 'product_items';

    protected $fillable = [
        'name',
        'display_name_ka',
        'display_name_en',
        'slug',
        'barcode',
        'category_id',
        'show',
        'unit_type',
        'brand_name',
        'unit',
        'has_image',
    ];

    protected $casts = [
        'name'            => 'string',
        'display_name_ka' => 'string',
        'display_name_en' => 'string',
        'slug'            => 'string',
        'barcode'         => 'string',
        'category_id'     => 'integer',
        'show'            => 'boolean',
        'has_image'       => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrandName(): ?string
    {
        return $this->brand_name;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name_ka;
    }

    public function getShow(): bool
    {
        return $this->show;
    }

    public function getBarCode(): string
    {
        return $this->barcode;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'item_id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
