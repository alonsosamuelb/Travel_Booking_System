<?php

namespace App\Services;

use App\Core\Auth;
use App\Models\ActivityLog;

class ActivityLogService
{
    public function log(string $action, string $entityType, ?int $entityId, string $description, ?int $userId = null): void
    {
        try {
            $resolvedUser = $userId ?? (Auth::user()['id'] ?? null);

            (new ActivityLog())->create([
                'user_id' => $resolvedUser,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            ]);
        } catch (\Throwable) {
            // Logging should never block the main action.
        }
    }
}
