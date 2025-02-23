<?php

declare(strict_types=1);

namespace App\Models\Currency;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property int currency_id
 * @property int provider_id
 * @property float buy_rate
 * @property float sell_rate
 */
class CurrencyRate extends Model
{
    public $timestamps = false;
    protected $table      = 'currency_rates';

    protected $fillable = [
        'currency_id',
        'provider_id',
        'buy_rate',
        'sell_rate',
        'date',
        'status',
    ];

    protected $casts = [
        'currency_id' => 'integer',
        'provider_id' => 'integer',
        'buy_rate'    => 'float',
        'sell_rate'   => 'float',
        'date'        => 'datetime',
        'status'      => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function getProviderId(): int
    {
        return $this->provider_id;
    }

    public function getBuyRate(): float
    {
        return $this->buy_rate;
    }

    public function getSellRate(): float
    {
        return $this->sell_rate;
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
