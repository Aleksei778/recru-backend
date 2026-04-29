<?php

declare(strict_types=1);

namespace App\Tenant\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

final class Tenant extends BaseTenant
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected static function newFactory(): TenantFactory
    {
        return TenantFactory::new();
    }

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
