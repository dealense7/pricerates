<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Contracts;

use Laravel\Passport\Token;

interface TokenRepositoryContract
{
    public function findById(string $id): ?Token;

    public function updateOtpVerified(string $tokenId, bool $otpVerified): int;
}
