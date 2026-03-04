<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

// Lightweight stub so PHPStan can resolve the service when the real class is excluded from analysis.
if (!class_exists(OrderCopilotService::class)) {
    class OrderCopilotService
    {
        public function respond(string $userMessage, array $catalog, ?User $user = null, array $sessionOrderIds = [], ?string $locale = null): string
        {
            return '';
        }
    }
}
