<?php

namespace App\Services;

use App\Models\UserHasShoogle;

/**
 * Class NotificService
 * @package App\Services
 */
class NotificService
{
    /**
     * NotificService constructor.
     */
    public function __construct()
    {
        null;
    }

    /**
     * Get a list of users needing notification.
     *
     * @return array
     */
    public function getLineUsers(): array
    {
        return UserHasShoogle::on()
            ->whereNull('left_at')
            ->where('is_reminder', '=', true)
            ->whereNotNull('reminder')
            ->get([
                'id',
                'user_id',
                'shoogle_id',
                'reminder',
                'reminder_interval',
                'last_notification',
                'in_process',
            ])->toArray();
    }
}
