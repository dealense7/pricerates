<?php

declare(strict_types=1);

namespace App\Models\Products;

use App\Models\Model;

/**
 * @property int id
 * @property int item_id
 * @property int provider_id
 * @property int store_id
 * @property int|null original_price
 * @property int current_price
 */
class Price extends Model
{
    protected $table    = 'product_prices';
    protected $fillable = [
        'item_id',
        'provider_id',
        'store_id',
        'original_price',
        'current_price',
    ];
    protected $casts    = [
        'item_id'        => 'int',
        'provider_id'    => 'int',
        'store_id'       => 'int',
        'original_price' => 'int',
        'current_price'  => 'int',
        'created_at'     => 'datetime',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function getProviderId(): int
    {
        return $this->provider_id;
    }

    public function getStoreId(): int
    {
        return $this->store_id;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->original_price;
    }

    public function getCurrentPrice(): int
    {
        return $this->current_price;
    }
}
