<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\User\UserContract;
use App\Models\Client\Company;
use App\Models\Model;
use App\Support\Traits\UserHasPermissions;
use App\Support\Traits\UserIdentities;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property int id
 * @property string username
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property integer region_id
 * @property integer company_id
 * @property Carbon deactivated_at
 * @property boolean uses_otp_check
 * @property string deactivation_reason
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, UserContract
{
    use Notifiable;
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasApiTokens;
    use UserHasPermissions;
    use SoftDeletes;
    use UserIdentities;

    public const PERMISSIONS_SCOPE = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'region_id',
        'company_id',
        'email',
        'password',
        'uses_otp_check',
        'deactivated_at',
        'deactivation_reason',
        'created_by',
        'updated_by',
        'deleted_by',
        'deactivated_by',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getRegionId(): int
    {
        return $this->region_id;
    }

    public function getCompanyId(): ?int
    {
        return $this->company_id;
    }

    public function getUsesOtpCheck(): bool
    {
        return $this->uses_otp_check;
    }

    public function contactInformation(): HasMany
    {
        return $this->hasMany(ContactInformation::class, 'user_id', 'id');
    }

    public function getIdentifier()
    {
        return $this->getKey();
    }

    public function getDeactivationReason(): ?string
    {
        return $this->deactivation_reason;
    }

    public function getDeactivatedAt(): ?string
    {
        return $this->deactivated_at?->format('Y-m-d H:i:s');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function casts(): array
    {
        return [
            'username'            => 'string',
            'first_name'          => 'string',
            'last_name'           => 'string',
            'region_id'           => 'integer',
            'company_id'          => 'integer',
            'deactivated_at'      => 'datetime',
            'deactivation_reason' => 'string',
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
        ];
    }
}
