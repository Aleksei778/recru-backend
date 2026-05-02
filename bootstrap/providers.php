<?php

declare(strict_types=1);

use App\Common\Providers\{
    AppServiceProvider,
    HorizonServiceProvider,
};
use App\Tenant\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    HorizonServiceProvider::class,
    TenancyServiceProvider::class,
];
