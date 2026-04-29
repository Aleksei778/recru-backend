<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

final class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::factory()->createMany([
            [
                'id' => 'acme-corp',
                'name' => 'Acme Corp',
                'subdomain' => 'acme',
                'website' => 'https://acme.example.com',
                'industry' => 'tech',
            ],
            [
                'id' => 'globex',
                'name' => 'Globex',
                'subdomain' => 'globex',
                'website' => 'https://globex.example.com',
                'industry' => 'finance',
            ],
        ]);
    }
}
