<?php

declare(strict_types=1);

namespace App\Exceptions\Request;

use Exception;

class InvalidatedRequestInputAccessException extends Exception
{
    protected $message = 'Accessing invalidated input not allowed';
}
