<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Tenant\Models\Tenant;
use App\User\Models\User;

trait WithTenant
{
    protected Tenant $tenant;
    protected User $user;

    protected function setUpTenant(): void
    {
        $this->tenant = Tenant::factory()->create(['subdomain' => 'acme']);
        $this->user = User::factory()->forTenant($this->tenant)->create();
    }

    protected function tenantUrl(string $path): string
    {
        return 'http://acme.localhost/' . ltrim($path, '/');
    }

    protected function tenantJson(string $method, string $path, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user, 'sanctum')
            ->json($method, $this->tenantUrl($path), $data);
    }
}