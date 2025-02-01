<?php

declare(strict_types=1);

return [
    'credentials'         => [
        'username'      => env('EVALUATION_USERNAME'),
        'password'      => env('EVALUATION_PASSWORD'),
        'grant_type'    => env('EVALUATION_GRANT_TYPE'),
        'client_id'     => env('EVALUATION_CLIENT_ID'),
        'client_secret' => env('EVALUATION_CLIENT_SECRET'),
    ],
    'private_key'         => env('PRIVATE_KEY'),
    'verify_public_key'   => env('EVALUATION_VERIFY_PUBLIC_KEY'),
    'evaluation_base_url' => env('EVALUATION_BASE_URL'),
    'endpoints'           => [
        'auth'        => sprintf('%s/oauth/token', env('EVALUATION_BASE_URL')),
        'get_clients' => sprintf('%s/client-integration/clients', env('EVALUATION_BASE_URL')),
    ],
];
