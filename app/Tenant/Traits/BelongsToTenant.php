<?php

declare(strict_types=1);

namespace App\Tenant\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder query()
 * @mixin Model
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $model->tenant_id = tenancy()->tenant?->id;
            }
        });

        static::addGlobalScope('tenant', function ($query) {
            if (tenancy()->initialized) {
                $query->where('tenant_id', tenancy()->tenant->id);
            }
        });
    }
}
