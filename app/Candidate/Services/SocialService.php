<?php

declare(strict_types=1);

namespace App\Candidate\Services;

use App\Candidate\Models\{Candidate, Social};

final readonly class SocialService
{
    public function syncSocials(Candidate $candidate, array $socials): void
    {
        foreach ($socials as $socialData) {
            Social::updateOrCreate(
                ['candidate_id' => $candidate->id, 'name' => $socialData['name']],
                ['url' => $socialData['url']]
            );
        }
    }
}
