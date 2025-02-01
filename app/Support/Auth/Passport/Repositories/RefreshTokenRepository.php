<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Support\Auth\Passport\Contracts\RefreshTokenRepositoryContract;
use Laravel\Passport\RefreshTokenRepository as BaseRefreshTokenRepository;

class RefreshTokenRepository extends BaseRefreshTokenRepository implements RefreshTokenRepositoryContract
{
    //
}
