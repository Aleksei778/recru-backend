<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Email\Enum\Type;
use App\Email\Models\Email;
use App\Interview\Models\Interview;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Seeder;

final class EmailSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $sender = User::where('tenant_id', $tenant->id)->first();
            $interviews = Interview::whereHas(
                'candidate',
                fn ($q) => $q->where('tenant_id', $tenant->id)
            )->with('candidate')->get();

            foreach ($interviews as $interview) {
                Email::factory()
                    ->forInterview($interview, $sender)
                    ->sent()
                    ->create([
                        'type' => Type::InterviewInvite->value,
                        'subject' => 'Приглашение на интервью',
                    ]);

                if (fake()->boolean(75)) {
                    Email::factory()
                        ->forInterview($interview, $sender)
                        ->sent()
                        ->create([
                            'type' => Type::QuestionsReady->value,
                            'subject' => 'Вопросы для интервью готовы',
                        ]);
                }

                if (fake()->boolean(50)) {
                    $type = fake()->boolean() ? Type::Approve : Type::Reject;
                    Email::factory()
                        ->forInterview($interview, $sender)
                        ->sent()
                        ->create([
                            'type' => $type->value,
                            'subject' => $type === Type::Approve ? 'Вы прошли отбор' : 'Результаты рассмотрения',
                        ]);
                }
            }
        }
    }
}
