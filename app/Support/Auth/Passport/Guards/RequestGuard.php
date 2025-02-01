<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Guards;

use Illuminate\Auth\RequestGuard as BaseRequestGuard;

class RequestGuard extends BaseRequestGuard
{
    public function unsetUser(): void
    {
        $this->user = null;
    }
}
