<?php

declare(strict_types=1);

namespace App\Tenant\Providers;

use Illuminate\Support\Facades\{Event, Route};
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\{Events, Listeners, Middleware\PreventAccessFromCentralDomains};
use App\Tenant\Http\Middleware\InitializeTenancyBySubdomain;

class TenancyServiceProvider extends ServiceProvider
{
    public function events(): array
    {
        return [
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],
        ];
    }

    public function boot(): void
    {
        $this->bootEvents();
        $this->mapRoutes();
    }

    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }
                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes(): void
    {
        $this->app->booted(function () {
            if (!file_exists(base_path('routes/tenant.php'))) {
                return;
            }

            foreach ($this->centralDomains() as $domain) {
                Route::middleware([
                    'api',
                    InitializeTenancyBySubdomain::class,
                    PreventAccessFromCentralDomains::class,
                ])
                    ->domain('{subdomain}.' . $domain)
                    ->group(base_path('routes/tenant.php'));
            }
        });
    }

    protected function centralDomains(): array
    {
        return config('tenancy.central_domains', []);
    }
}
