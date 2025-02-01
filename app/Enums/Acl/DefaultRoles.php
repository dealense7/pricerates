<?php

declare(strict_types=1);

namespace App\Enums\Acl;

enum DefaultRoles: string
{
    case MODERATOR      = 'default_moderator';
    case INITIATOR      = 'default_initiator';
    case REPRESENTATIVE = 'default_representative';
}
