<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Models\Model;

/**
 * @property int id
 * @property string name
 * @property boolean status
 */
class Provider extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'name'   => 'string',
        'status' => 'boolean',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }
}
