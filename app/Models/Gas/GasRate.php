<?php

declare(strict_types=1);

namespace App\Models\Gas;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string name
 * @property string tag
 * @property int provider_id
 * @property int price
 * @property Carbon date
 */
class GasRate extends Model
{
    protected $table = 'gas_rates';

    protected $fillable = [
        'provider_id',
        'name',
        'price',
        'date',
        'status',
        'tag',
    ];

    protected $casts = [
        'provider_id' => 'integer',
        'name'        => 'string',
        'tag'        => 'string',
        'price'       => 'integer',
        'date'        => 'date',
        'status'      => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->provider_id;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getTag(): string
    {
        return $this->tag;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
