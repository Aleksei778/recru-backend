<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Tenant\Models\Tenant;
use App\User\Models\User;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Seeder;

final class VacancySeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $admin = User::where('tenant_id', $tenant->id)->first();

            Vacancy::factory()
                ->published()
                ->forTenant($tenant, $admin)
                ->count(3)
                ->create();

            Vacancy::factory()
                ->forTenant($tenant, $admin)
                ->count(2)
                ->create();
        }
    }
}
