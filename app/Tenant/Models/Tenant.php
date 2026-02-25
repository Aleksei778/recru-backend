<?php

declare(strict_types=1);

namespace App\Tenant\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'website',
            'industry',
            'data',
        ];
    }

    protected $fillable = [
        'name',
        'website',
        'industry',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }
}
