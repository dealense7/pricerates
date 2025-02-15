<?php

declare(strict_types=1);

namespace App\Models\Store;

use App\Models\Model;

/**
 * @property integer id
 * @property integer provider_id
 * @property integer store_id
 * @property array meta
 */
class Url extends Model
{
    protected $table = 'store_urls';

    protected $fillable = [
        'provider_id',
        'store_id',
        'meta',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'store_id'    => 'integer',
        'meta'        => 'array',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->provider_id;
    }

    public function getStoreId(): int
    {
        return $this->store_id;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }
}
