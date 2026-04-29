<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            User::factory()->admin()->forTenant($tenant)->create([
                'email' => 'admin@' . $tenant->subdomain . '.test',
            ]);

            User::factory()->hr()->forTenant($tenant)->count(2)->create();
        }
    }
}
