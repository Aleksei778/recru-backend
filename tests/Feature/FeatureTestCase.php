<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Tenant\Models\Tenant;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithTenant;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase, WithTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::factory()->create();
        tenancy()->initialize($tenant);
    }
}
