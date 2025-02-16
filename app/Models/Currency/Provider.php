<?php

declare(strict_types=1);

namespace App\Models\Currency;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string title
 * @property string name
 * @property bool status
 * @property string logo_url
 */
class Provider extends Model
{
    use SoftDeletes;

    protected $table = 'currency_providers';

    protected $fillable = [
        'title',
        'name',
        'status',
        'logo_url',
    ];

    protected $casts = [
        'title'    => 'string',
        'name'     => 'string',
        'logo_url' => 'string',
        'status'   => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogoUrl(): string
    {
        return $this->logo_url;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function rates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class, 'provider_id');
    }
}
