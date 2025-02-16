<?php

declare(strict_types=1);

namespace App\Models\Currency;


use App\Models\Model;

/**
 * @property int id
 * @property string code
 * @property string name
 */
class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'name' => 'string',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
