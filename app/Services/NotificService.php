<?php

namespace App\Services;

use App\Helpers\HelperNow;
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
//                        $query->whereDate('reminder', '<', Carbon::now());
                        $query->whereDate('reminder', '<', HelperNow::getCarbon());
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
        // Cut off a single event that has not yet occurred
        // last_notification skip if less than a day
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
            ->update(['in_process' => HelperNow::getCarbon()]);
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
            ->update(['last_notification' => HelperNow::getCarbon()]);
    }

    /**
     * Do I need to send a notification?
     *
     * @param string $reminder
     * @param string|null $reminderInterval
     * @param string|null $lastNotification
     * @return bool
     * @throws \Recurr\Exception\InvalidWeekday
     */
    public function needToSend(string $reminder, ?string $reminderInterval, ?string $lastNotification): bool
    {
        $nowTimestamp = HelperNow::getTimestamp();
//        $nowTimestamp = Carbon::now()->timestamp;
        $reminderTimestamp = strtotime($reminder);

        // if the event is single
        if ( empty( $reminderInterval ) ) {
            if ( empty( $lastNotification ) ) {
                if ( $nowTimestamp >= $reminderTimestamp ) {
                    return true;
                }
            }
        } else {
            return HelperRrule::eventHasCome($reminder, $reminderInterval, $lastNotification);
        }

        return false;
    }
}
