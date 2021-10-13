<?php

namespace App\Services;

use App\Helpers\HelperRrule;
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
            ->where(function ($query) {
                $query->whereNotNull('reminder_interval')
                    ->orWhere(function ($query) {
                        $query->whereDate('reminder', '<', Carbon::now());
                    });
            })
            ->get([
                'id',
                'user_id',
                'shoogle_id',
                'reminder',
                'reminder_interval',
                'last_notification',
                'in_process',
            ])->toArray();
        // Отсекать единичное событие которое еще не наступило
        // last_notification если меньше суток то пропускать
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
     * @param int $userHasShoogleId
     */
    public function unlockUserHasShoogle(int $userHasShoogleId)
    {
        UserHasShoogle::on()
            ->where('id', '=', $userHasShoogleId)
            ->update(['in_process' => null]);
    }

    /**
     * Date the time of the last notification.
     *
     * @param int $userHasShoogleId
     */
    public function putNowLastNotification(int $userHasShoogleId)
    {
        UserHasShoogle::on()
            ->where('id', '=', $userHasShoogleId)
            ->update(['last_notification' => Carbon::now()]);
    }

    /**
     * Date unlock time.
     *
     * @param int $userHasShoogleId
     */
    public function putNowInProcess(int $userHasShoogleId)
    {
        UserHasShoogle::on()
            ->where('id', '=', $userHasShoogleId)
            ->update(['in_process' => Carbon::now()]);
    }

    /**
     * Do I need to send a notification?
     *
     * @param string $reminder
     * @param string|null $reminderInterval
     * @param string|null $lastNotification
     * @return bool
     */
    public function needToSend(string $reminder, ?string $reminderInterval, ?string $lastNotification): bool
    {
        $nowTimestamp = Carbon::now()->timestamp;
        $reminderTimestamp = strtotime($reminder);

        // если событие единичное
        if ( empty( $reminderInterval ) ) {
            if ( empty( $lastNotification ) ) {
                if ( $nowTimestamp >= $reminderTimestamp ) {
                    return true;
                }
            }
        } else {
//            return false;
            return HelperRrule::eventHasCome($reminder, $reminderInterval, $lastNotification);
        }

        return false;
    }
}
