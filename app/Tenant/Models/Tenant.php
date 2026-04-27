<?php

declare(strict_types=1);

namespace App\Tenant\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

final class Tenant extends BaseTenant
{
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'subdomain',
            'website',
            'industry',
            'data',
        ];
    }

    protected $fillable = [
        'name',
        'website',
        'subdomain',
        'industry',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
