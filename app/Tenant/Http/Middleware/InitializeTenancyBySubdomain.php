<?php

declare(strict_types=1);

namespace App\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Tenancy;
use App\Tenant\Models\Tenant;

final class InitializeTenancyBySubdomain
{
    public function __construct(
        private Tenancy $tenancy
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $host = $request->getHost();
        $central = config('tenancy.central_domains');
        $subdomain = null;

        foreach ($central as $domain) {
            if (str_ends_with($host, '.' . $domain)) {
                $subdomain = str_replace('.' . $domain, '', $host);
                break;
            }
        }

        if (!$subdomain) {
            abort(404);
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404);
        }

        $this->tenancy->initialize($tenant);

        return $next($request);
    }
}
