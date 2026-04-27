<?php

declare(strict_types=1);

use App\Common\Providers\AppServiceProvider;
use App\Tenant\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
];
