<?php

declare(strict_types=1);

use App\Tenant\Models\Tenant;

return [
    'tenant_model' => Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'central_domains' => [
        '127.0.0.1',
        'localhost',
        'recru.local',
    ],

    'bootstrappers' => [],

    'features' => [],

    'routes' => false,

    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations')],
        '--realpath' => true,
    ],

    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
    ],
];
