<?php

declare(strict_types=1);

namespace App\Repositories\V1\Auth;

use App\Support\Auth\Passport\Contracts\ClientRepositoryContract;
use Laravel\Passport\ClientRepository as BaseClientRepository;

class ClientRepository extends BaseClientRepository implements ClientRepositoryContract
{
    //
}
