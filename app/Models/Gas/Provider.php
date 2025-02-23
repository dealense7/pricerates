<?php

declare(strict_types=1);

namespace App\Models\Gas;

use App\Models\Model;

/**
 * @property int id
 * @property string name
 * @property boolean status
 */
class Provider extends Model
{
    protected $table = 'gas_providers';

    protected $fillable = [
        'id',
        'name',
        'status',
        'logo_url',
    ];

    protected $casts = [
        'status'   => 'boolean',
        'name'     => 'string',
        'logo_url' => 'string',
    ];

    public function getId(): int
    {
        return $this->id;
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
}
