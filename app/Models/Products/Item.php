<?php

declare(strict_types=1);

namespace App\Models\Products;

use App\Models\General\Category;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'unit',
    ];

    protected $casts = [
        'name'            => 'string',
        'display_name_ka' => 'string',
        'display_name_en' => 'string',
        'slug'            => 'string',
        'barcode'         => 'string',
        'category_id'     => 'integer',
        'show'            => 'boolean',
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
}
