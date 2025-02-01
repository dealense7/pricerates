<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Support\Auth\Passport\Contracts\TokenRepositoryContract;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository as BaseTokenRepository;

class TokenRepository extends BaseTokenRepository implements TokenRepositoryContract
{
    public function findById(string $id): ?Token
    {
        return parent::find($id);
    }

    public function updateOtpVerified(string $tokenId, bool $otpVerified): int
    {
        return Passport::token()->where('id', $tokenId)->update(['otp_verified' => $otpVerified]);
    }
}
