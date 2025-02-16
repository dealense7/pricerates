<?php

declare(strict_types=1);

namespace App\Models\General;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'url',
        'disk',
        'extension',
        'size',
        'fileable_type',
        'fileable_id',
    ];

    protected $casts = [
        'size'          => 'float',
        'fileable_type' => 'string',
        'fileable_id'   => 'integer',
        'disk'          => 'string',
        'extension'     => 'string',
        'url'           => 'string',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
