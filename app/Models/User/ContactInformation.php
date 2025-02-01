<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\User\ContactType;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int user_id
 * @property ContactType type
 * @property string data
 * @property boolean is_default
 */
class ContactInformation extends Model
{
    use SoftDeletes;

    protected $table = 'contact_information';

    protected $fillable = [
        'user_id',
        'type',
        'data',
        'is_default',
    ];

    protected $casts = [
        'user_id'    => 'integer',
        'type'       => ContactType::class,
        'data'       => 'string',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getIsDefault(): bool
    {
        return $this->is_default;
    }

    public function getType(): ContactType
    {
        return $this->type;
    }

    public function getTypeToText(): string
    {
        return $this->type->getText();
    }

    public function getData(): string
    {
        return $this->data;
    }
}
