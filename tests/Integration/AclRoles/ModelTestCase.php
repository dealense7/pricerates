<?php

declare(strict_types=1);

namespace Tests\Integration\AclRoles;

use App\Models\Acl\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Role
    {
        return new Role();
    }
}
