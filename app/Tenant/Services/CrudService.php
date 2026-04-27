<?php

declare(strict_types=1);

namespace App\Tenant\Services;

use App\Tenant\Models\Tenant;

final readonly class CrudService
{
    public function create(string $name, string $subdomain): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'subdomain' => $subdomain,
        ]);
    }
}
