<?php

namespace App\Services;

use App\Models\UserHasShoogle;
use Carbon\Carbon;

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

    /**
     * Get member ids from member array.
     *
     * @param array $lineUsers
     * @return array
     */
    public function getUserHasShoogleIds(array $lineUsers): array
    {
        return array_map(function ($item) {
            return $item['id'];
        }, $lineUsers);
    }

    /**
     * Block processed members.
     *
     * @param array $userHasShoogleIds
     */
    public function lockUserHasShoogle(array $userHasShoogleIds)
    {
        UserHasShoogle::on()
            ->whereIn('id', $userHasShoogleIds)
            ->update(['in_process' => Carbon::now()]);
    }

    /**
     * Unblock processed members.
     *
     * @param array $userHasShoogleIds
     */
    public function unlockUserHasShoogle(array $userHasShoogleIds)
    {
        UserHasShoogle::on()
            ->whereIn('id', $userHasShoogleIds)
            ->update(['in_process' => null]);
    }

    public function needToSend($reminder, string $reminderInterval, $lastNotification): bool
    {
        return true;
    }
}
