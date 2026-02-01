<?php

declare(strict_types=1);

namespace App\Tenant\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'slug',
        'company_name',
        'website',
        'industry',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];
}
