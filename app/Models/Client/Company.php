<?php

declare(strict_types=1);

namespace App\Models\Client;

use App\Models\Model;

/**
 * @property int id
 * @property string name
 * @property string display_name
 */
class Company extends Model
{
    protected $table = 'client_companies';

    protected $fillable = [
        'name',
        'display_name',
    ];

    protected $casts = [
        'display_name' => 'string',
        'name'         => 'string',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }
}
