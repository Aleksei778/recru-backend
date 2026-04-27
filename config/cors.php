<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
    ],

    'allowed_origins_patterns' => [
        '#^https?://[a-z0-9-]+\.recru\.local(:\d+)?$#',
        '#^https?://[a-z0-9-]+\.' . preg_quote(env('APP_DOMAIN', 'recru.app')) . '$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
