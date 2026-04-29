<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Candidate\Models\Candidate;
use App\Candidate\Models\Social;
use App\Candidate\Models\WorkPlace;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Seeder;

final class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $hr = User::where('tenant_id', $tenant->id)->first();

            Candidate::factory()
                ->forTenant($tenant, $hr)
                ->count(10)
                ->create()
                ->each(function (Candidate $candidate) {
                    WorkPlace::factory()
                        ->count(fake()->numberBetween(1, 3))
                        ->create(['candidate_id' => $candidate->id]);

                    Social::factory()
                        ->count(fake()->numberBetween(1, 2))
                        ->create(['candidate_id' => $candidate->id]);
                });
        }
    }
}
